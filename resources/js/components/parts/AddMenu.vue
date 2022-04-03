<template>
    <div class="modal fade" id="add-menu" ref="addMenuModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Menu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addMenuForm" @submit.prevent="postForm" method="post" :action="this.$parent.add_menu_action" enctype="multipart/form-data">
                    <div class="modal-body">
                        <validation-errors :errors="validationErrors" v-if="validationErrors"></validation-errors>
                        <input type="hidden" name="food_category_id" :value="food_category_id">

                        <fieldset v-for="input in inputs" :key="input.id">
                            <div class="single-food" :ref="input.id">
                                <legend>Add menu <button class="btn btn-sm btn-danger" @click.prevent="removeMenu(input.id)">Remove</button></legend>
                                <div class="form-group">
                                    <label for="menu-name">Menu name</label>
                                    <input type="text" :placeholder="input.name" name="food_name[]" id="menu_name" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="menu-price">Menu price</label>
                                    <input type="text" :placeholder="input.price" name="unit_price[]" id="menu_price" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="menu-description">Description</label>
                                    <input type="text" :placeholder="input.description" name="description[]" id="description" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="menu-image">Menu image</label>
                                    <input type="file" name="food_image_url[]" :id="'menu_image_'+input.id" class="form-control-file" accept="image/*">
                                </div>
                            </div>
                        </fieldset>
                        <button class="btn btn-primary" @click.prevent="addAnotherFood">Add New</button>
                    </div>
                    <div class="modal-footer">
                        <span><i class="fa fa-spinner fa-spin" v-if="loading"></i></span>
                        <input type="submit" class="btn btn-primary" value="Save"/>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Back</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'add-menu',
        props: ['food_category_id'],
        data(){
            return {
                counter: 0,
                inputs: [{
                    id: 'food_0',
                    name: 'Enter name of food',
                    price: 'Enter price',
                    description: 'Enter details of food items'
                }],
                validationErrors: '',
                loading: false
            }
        },
        mounted(){
            $(this.$refs.addMenuModal).on("hidden.bs.modal", this.resetForm);
        },
        methods: {
            addAnotherFood(){
                this.inputs.push({
                    id: `food_${++this.counter}`,
                    name: 'Enter name of food',
                    price: 'Enter price',
                    description: 'Enter details of food items'
                })
            },

            removeMenu(id){
                this.inputs.splice(_.findIndex(this.inputs, o=>{
                    return o.id == id;
                }), 1);
            },

            resetForm(){
                this.inputs = [];
                this.validationErrors = '';
                this.counter = 0;
                this.$parent.adding.menuCategory = -1;
                this.loading = false;
            },


            postForm(){
                let form = document.getElementById('addMenuForm');
                let formData = new FormData(form);
                formData.delete('food_image_url[]');
                
                let counter = 0;
                this.inputs.forEach(input=>{
                    let img = document.querySelector('#menu_image_'+input.id).files[0];
                    img = img === undefined ? null : img;
                    if (img) { formData.append('food_image_url['+(counter++)+']', img); }
                    else { formData.append('food_image_url['+(counter++)+']', null); }
                })

                this.loading = true;
                axios.post(this.$parent.add_menu_action, formData, {
                    headers: {
                    'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    if(response.data.message == "success"){
                        this.$parent.foodAndCategories = response.data.payload;
                        $(this.$refs.addMenuModal).modal("hide");
                    }
                    this.loading = false;
                }).catch(error => {
                    if (error.response && error.response.status == 422){
                        this.validationErrors = error.response.data.errors;
                    }
                    this.loading = false;
                });
            }
        },
        components: {
            ValidationErrors: require('../ValidationErrors.vue')
        }
    }
</script>

