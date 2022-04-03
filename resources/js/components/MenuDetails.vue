<template>
    <div class="view">
        <add-category></add-category>
        <edit-category :category_id="editing.category.id" :category_name="editing.category.name"></edit-category>
        <add-menu :food_category_id="adding.menuCategory"></add-menu>
        <edit-menu :site_url='site_url'></edit-menu>
        
        <div class="card">
            <div class="card-body">
                <div class="float-right">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-category">Add Category</button>
                </div>
                
                <div v-if="foodCategories.length > 0">
                    <div id="item-menu" v-for="category in foodCategories" v-bind:key="category.food_category_id">
                        <div id="item-header">
                            {{ category.food_category_name }}
                            <div class="float-right" style="min-width: 330px;">
                                <button type="button" @click="adding.menuCategory = category.food_category_id" class="btn btn-outline-primary" data-toggle="modal" data-target="#add-menu">Add menu</button>
                                <button type="button" @click="editCategory(category.food_category_id)" class="btn btn-outline-primary" data-toggle="modal" data-target="#edit-category">Edit Category</button>
                                <button type="button" @click="deleteCategory(category.food_category_id)" class="btn btn-danger">Delete Category</button>
                            </div>
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
                                        <td>
                                            <a href="#" @click.prevent="setEditingMenu(food.food_id)" class="btn btn-link" data-toggle="modal" data-target="#edit-menu">Edit</a>
                                        </td>
                                        <td>
                                            <a href="#" @click.prevent="deleteMenu(food.food_id)" class="btn btn-link">Delete</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <h5>Add some foods to manage them here.</h5>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'menu-details',
        props: ['add_category_action', 'edit_category_action', 'add_menu_action', 'edit_menu_action', 'delete_menu_action', 'toggle_menu_action', 'foodandcategories', 'site_url', 'food_category_list'],
        data(){
            return {
                foodAndCategories: [],
                foodCategoryList: [],

                editing: {
                    menu: -1,
                    category: {
                        id: -1,
                        name: null
                    }
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

            getEditingMenu(){
                let index = _.findIndex(this.foodAndCategories, f=>{return f.food_id == this.editing.menu});
                if (index != -1){
                    return this.foodAndCategories[index];
                }
                return null;
            },

            getAddingMenuCategory(){

            }
        },
        components: {
            AddCategory: require('./parts/AddCategory.vue'),
            EditCategory: require('./parts/EditCategory.vue'),
            AddMenu: require('./parts/AddMenu.vue'),
            EditMenu: require('./parts/EditMenu.vue'),
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

            setEditingMenu(id){
                this.editing.menu = id;
            },

            updateSingleMenu(food_id, payload){
                // console.log(food_id);
                // console.log(payload);
                let index = _.findIndex(this.foodAndCategories, f=>{return f.food_id == food_id});
                if (index != -1){
                    this.foodAndCategories.splice(index, 1, payload);
                }
            },

            addSingleMenu(payload){
                this.foodAndCategories.splice(this.foodAndCategories.length, 0, payload);
            },

            deleteMenu(food_id){
                if (confirm('Are you sure you want to delete this?')){
                    axios.post(this.delete_menu_action, {
                        food_id: food_id
                    }).then(response => {
                        if(response.data.message == "success"){
                            let index = _.findIndex(this.foodAndCategories, f=>{return f.food_id == food_id});
                            this.foodAndCategories.splice(index, 1);
                        }
                    }).catch(error => {
                        // console.log(error);
                    });
                }
            },

            toggleMenu(food_id){
                    axios.post(this.toggle_menu_action, {
                        food_id: food_id
                    }).then(response => {
                        // do nothing, just send the message to server
                    }).catch(error => {
                        // console.log(error);
                    });
            },

            editCategory(category_id){
                console.log("Working");
                let index = _.findIndex(this.foodCategoryList, f=>{return f.food_category_id == category_id});
                if (index != -1){
                    this.editing.category.id = this.foodCategoryList[index].food_category_id;
                    this.editing.category.name = this.foodCategoryList[index].food_category_name;
                } else {
                    console.log('found nothing');
                }
            },

            deleteCategory(category_id){
                let sure = confirm("Are you sure you want to delete this category and all foods under it?");

                if (sure){
                    axios.post(this.edit_category_action, {
                        action: 'delete',
                        category_id: category_id
                    }).then(response => {
                        if(response.data.message == "success"){
                            this.foodAndCategories = response.data.payload;
                        }
                        this.loading = false;
                    }).catch(error => {
                        if (error.response && error.response.status == 422){
                            this.validationErrors = error.response.data.errors;
                        }
                        this.loading = false;
                    });
                }
            }
        }
    }
</script>
