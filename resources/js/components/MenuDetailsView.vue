<template>
    <div class="view">
        <div class="card">
            <div class="card-body">
                <div v-if="foodCategories.length > 0">
                    <div id="item-menu" v-for="category in foodCategories" v-bind:key="category.food_category_id">
                        <div id="item-header">
                            {{ category.food_category_name }}
                        </div>
                        <div id="item-body">
                            <table class="table table-sm">
                                <tbody>
                                    <tr v-for="food in getFoodsByCategory(category.food_category_id)" v-bind:key="food.food_id">
                                        <td>
                                            <label class="switch">
                                                <input @change="toggleMenu(food.food_id)" type="checkbox" v-model="food.food_availability">
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td>{{ food.food_name }}</td>
                                        <td>BDT {{ food.unit_price }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <h5>No food added.</h5>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'menu-details',
        props: ['toggle_menu_action', 'foodandcategories', 'site_url', 'food_category_list'],
        data(){
            return {
                foodAndCategories: [],
                foodCategoryList: [],

                editing: {
                    menu: -1
                },

                adding: {
                    menuCategory: -1
                }
            }
        },
        mounted(){
            this.getFoodAndCategories();
            this.getFoodCategoryList();
        },
        computed: {
            foodCategories(){
                return _.sortBy(_.uniqBy(this.foodAndCategories, 'food_category_id'), ['food_category_name']);
            },
        },
        methods: {
            getFoodAndCategories(){
                axios.get(this.foodandcategories)
                    .then(response=>{
                        this.foodAndCategories = response.data;
                    })
                    .catch(e=>{
                        console.log(e);
                    });
            },

            getFoodCategoryList(){
                axios.get(this.food_category_list)
                    .then(response=>{
                        this.foodCategoryList = response.data;
                    })
                    .catch(e=>{
                        console.log(e);
                    });
            },

            getFoodsByCategory(category_id){
                return _.filter(this.foodAndCategories, function(o){
                    return o.food_category_id == category_id;
                })
            },

            toggleMenu(food_id){
                    axios.post(this.toggle_menu_action, {
                        food_id: food_id
                    }).then(response => {
                        // do nothing, just send the message to server
                    }).catch(error => {
                        // console.log(error);
                    });
            }
        }
    }
</script>
