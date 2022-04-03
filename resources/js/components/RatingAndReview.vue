<template>
    <div id="container">
        <div class="view" id="view-rating">
            <h1>Rating &amp; Reviews</h1>
            <div class="card">
                <div class="card-body">
                    <div class="card-list">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-light active">
                                <input type="radio" name="ratings" class="menu" id="rating-restaurant" autocomplete="off" checked>
                                Restaurant
                            </label>
                            <label class="btn btn-light">
                                <input type="radio" name="ratings" class="menu" id="rating-menu" autocomplete="off">
                                Menu
                            </label>
                        </div>
                    </div>
                    <div class="rating" id="view-rating-restaurant">
                        <div class="card-list" v-for="rating in restRating.data" v-bind:key='rating.id'>
                            <div class="row">
                                <div class="col-md-2 text-center">
                                    <img :src="rating.user_img" class="avatar-img" style="height: 5rem;">
                                </div>
                                <div class="col-md-6">
                                    <div class="rating-header">
                                        {{ rating.user_name }}
                                    </div>
                                    <div class="rating-star">
                                        <span v-for="x in (rating.starsGiven-0)" :key='"given-"+x' class="fas fa-star active" id="rating-stars"></span>
                                        <span v-for="y in (5-rating.starsGiven)" :key='"remains"+y' class="far fa-star" id="rating-stars"></span> 
                                    </div>
                                    <div v-if="rating.has_review">
                                        <div>
                                            <div class="rating-text">
                                                {{ rating.review_text }}
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div>
                                            <div v-if="rating.has_reply" style="font-size:12px;">
                                                Reply: {{ rating.review_reply.reply_text }}
                                            </div>
                                            <div v-if="!isadmin">
                                                <div v-if="replyform[rating.id] && rating.has_review && !rating.has_reply">
                                                    <textarea v-model="replytext[rating.id]" name="reply" ref="'rating'+rating.id" cols="30" rows="10" placeholder="Write your reply" class="form-control reply-area"></textarea>
                                                    <button class="btn btn-primary" @click.prevent.stop="submitReply(rating.id)">Submit</button>
                                                </div>
                                                <div v-if="rating.has_review && !rating.has_reply">
                                                    <a href="#" @click.prevent.stop="toggleReplyForm(rating.id)" style="color:#019be1">{{ (replyform[rating.id] && rating.has_review) ? "Close" : "Reply" }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="rating-date float-right">
                                        {{ rating.created_at }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <pagination class="mt-2" :data="restRating" 
                            @pagination-change-page="getRestRatings"
                            :show-disabled='true'
                            :limit = '2'
                            ></pagination>
                    </div>
                    <div class="rating" id="view-rating-menu">
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <select name="food_category" id="food_category" class="form-control" v-model="selectedCategory" @change="updateNames(selectedCategory)">
                                    <option value="-1">Select Category</option>
                                    <option v-for="category in foodCategories" :key="category.food_category_id" :value="category.food_category_id">
                                        {{ category.food_category_name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select name="food_name" id="food_name" class="form-control" v-model="selectedFood" @change="loadRatingsFor(selectedFood)">
                                    <option value="-1">Select Food</option>
                                    <option v-for="food in foodNames" :key="food.food_id" :value="food.food_id">
                                        {{ food.food_name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <h4 class="mt-4" v-if="selectedFood !== -1">Showing ratings for {{ category_name }} > {{ food_name }}</h4>
                        <div class="food-ratings-container mt-3">
                            <food-ratings
                                :fetchfrom='fooddataurl'
                            ></food-ratings>
                        </div>
                        <!-- <ul class="rating-menu-list" style="margin-top: 30px;">
                            <div v-for="category in foodCategories" :key='category.food_category_id'>
                                <a :href="'#menu-'+category.food_category_id+'-list-item'" data-toggle="collapse" data-parent="#view-rating-menu" aria-expanded="false">
                                    <li class="rating-menu-list-item">
                                        {{ category.food_category_name }}
                                    </li>
                                </a>
                                <li>
                                    <div class="collapse" :id="'menu-'+category.food_category_id+'-list-item'">
                                        <ul class="rating-menu-list">
                                            <div v-for="food in getFoodsByCategory(category.food_category_id)" :key='food.food_id'>
                                                <a :href="'#food-'+food.food_id+'-list-item'" data-toggle="collapse" aria-expanded="false" @click="loadRatingsFor(food.food_id)">
                                                    <li class="rating-menu-list-item">
                                                        {{ food.food_name }}
                                                    </li>
                                                </a>
                                                <li>
                                                    <div class="collapse" :id="'food-'+food.food_id+'-list-item'">
                                                        <ul class="rating-menu-list">
                                                            <li>
                                                                <food-ratings 
                                                                    :foodid='food.food_id'
                                                                    :fetchfrom='fooddataurl+"?food_id="+food.food_id'
                                                                ></food-ratings>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                            </div>
                                        </ul>
                                    </div>
                                </li>
                            </div>

                        </ul> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="css">
    .reply-area {
        height:70px !important;
    }
</style>

<script>
    import axios from 'axios';
    import _ from 'lodash';

    export default {
        name: 'rating-and-review',
        props: ['restaurantdataurl', 'foodandcategories', 'fooddataurl', 'postreplyurl', 'postfoodreplyurl', 'isadmin'],
        data(){
            return {
                restRating: {},
                foodAndCategories: [],
                foodNames: [],
                selectedCategory: -1,
                selectedFood: -1,
                category_name: null,
                food_name: null,
                replyform: [],
                replytext: []
            }
        },
        computed: {
            foodCategories(){
                return _.uniqBy(this.foodAndCategories, 'food_category_id');
            }
        },
        mounted(){
            this.getRestRatings();
            this.getFoodAndCategories();
        },
        components: {
            FoodRatings: require("./FoodRatings.vue")
        },
        methods: {
            getRestRatings(page=1){
                axios.get(this.restaurantdataurl+"?page="+page)
                    .then(response=>{
                        this.restRating = response.data;
                    })
                    .catch(e=>{
                        console.log(e);
                    });
            },

            getFoodAndCategories(){
                axios.get(this.foodandcategories)
                    .then(response=>{
                        this.foodAndCategories = response.data;
                    })
                    .catch(e=>{
                        console.log(e);
                    });
            },

            getFoodsByCategory(category_id){
                return _.filter(this.foodAndCategories, function(o){
                    return o.food_category_id == category_id;
                });
            },
            
            getFoodById(food_id){
                return _.find(this.foodAndCategories, function(o){
                    return o.food_id == food_id;
                });
            },

            loadRatingsFor(food_id){
                let food = this.getFoodById(food_id);
                // console.log(food);
                this.category_name = food.food_category_name;
                this.food_name = food.food_name;
                this.$emit('loadFoodRatings', {food_id});
            },

            updateNames(category){
                // console.log("updaging names");
                this.foodNames = this.getFoodsByCategory(category);
                this.selectedFood = -1;
            },

            toggleReplyForm(id){
                // console.log("editing "+id);
                if (!this.replyform[id]){
                    Vue.set(this.replyform, id, true);
                } else {
                    Vue.set(this.replyform, id, false);
                }
            },

            submitReply(id){
                let reply = this.replytext[id];

                axios.post(this.postreplyurl, {
                    'reply' : reply,
                    'rating_id' : id
                }).then(response=>{
                        // console.log(response);
                        if (response.data.success){
                            let index = _.findIndex(this.restRating.data, function(o) { return o.id == id; });
                            this.restRating.data[index].has_reply = true;
                            this.restRating.data[index].review_reply = response.data.data;
                            Vue.set(this.replyform, id, false);
                        }
                    })
                    .catch(e=>{
                        console.log(e);
                    });
            }
        }
    }
</script>
