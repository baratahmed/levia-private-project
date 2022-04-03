<template>
    <div class="food-rating-root">
        <div class="card-list" v-for="rating in foodRatings.data" :key="rating.id">
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
                            <div v-if="!$parent.$props.isadmin">
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
        <pagination class="mt-2" :data="foodRatings" 
            @pagination-change-page="loadData"
            :show-disabled='true'
            :limit = '2'
        ></pagination>
    </div>
</template>

<script>
    import axios from 'axios';
    import _ from 'lodash';

    export default {
        name: 'food-ratings',
        props: ['fetchfrom'],
        data(){
            return {
                initialDataReceived : false,
                foodRatings : {},
                currentFood: -1,
                replyform: [],
                replytext: []
            }
        },
        methods: {
            loadData(page=1){
                let fetchfrom = this.fetchfrom+"?food_id="+this.currentFood;

                axios.get(fetchfrom+"&page="+page)
                    .then(response=>{
                        this.foodRatings = response.data;
                        this.initialDataReceived = true;
                    })
                    .catch(e=>{
                        console.log(e);
                    });
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

                axios.post(this.$parent.$props.postfoodreplyurl, {
                    'reply' : reply,
                    'rating_id' : id
                }).then(response=>{
                        // console.log(response);
                        if (response.data.success){
                            let index = _.findIndex(this.foodRatings.data, function(o) { return o.id == id; });
                            this.foodRatings.data[index].has_reply = true;
                            this.foodRatings.data[index].review_reply = response.data.data;
                            Vue.set(this.replyform, id, false);
                        }
                    })
                    .catch(e=>{
                        console.log(e);
                    });
            }
        },
        mounted() {
            let self = this;
            this.$parent.$on('loadFoodRatings', function(data){
                // window.console.log(data.food_id, self.foodid, self.initialDataReceived);
                if (self.currentFood !== data.food_id){
                    self.initialDataReceived = false;
                }

                if (!self.initialDataReceived){
                    window.console.log('fetching for ' + data.food_id);
                    self.currentFood = data.food_id;
                    self.loadData();
                }
            })
        }
    }
</script>