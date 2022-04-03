<template>
    <div class="modal fade" id="add-category" ref="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" @submit.prevent="submitForm" :action="this.$parent.add_category_action" enctype="multipart/form-data" ref="addCategoryForm">
                    <div class="modal-body">
                        <validation-errors :errors="validationErrors" v-if="validationErrors"></validation-errors>
                        <div class="form-group">
                            <label for="category">Category Name</label>
                            <vue-select v-model="selected" id="category" autofocus taggable :placeholder="placeholder" 
                            :options="getCategories"></vue-select>
                            <input type="hidden" name="categoryId" :value="selected.id">
                            <input type="hidden" name="categoryName" :value="selected.label">
                        </div>
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
                        <input type="submit" class="btn btn-primary" value="Add"/>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Back</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import vSelect from 'vue-select';

    export default {
        name: 'add-category',
        components: {
            'vue-select' : vSelect,
            ValidationErrors: require('../ValidationErrors.vue')
        },
        mounted(){
            $(this.$refs.addCategoryModal).on("hidden.bs.modal", this.resetForm);
        },
        data(){
            return {
                placeholder: "Enter the name of category or select one.",
                counter: 0,
                inputs: [{
                    id: 'food_0',
                    name: 'Enter name of food',
                    price: 'Enter price',
                    description: 'Enter details of food items'
                }],
                selected: {id:null, label: null},
                validationErrors: '',
                loading: false
            }
        },
        computed: {
            getCategories(){
                return _.map(this.$parent.foodCategoryList, category => {
                    return {
                        id: category.food_category_id,
                        label: category.food_category_name
                    }
                });
            }
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
                this.selected = {id:null, label: null};
                this.counter = 0;
            },

            submitForm(){
                let form = this.$refs.addCategoryForm;
                let formData = new FormData(form);
                console.log(formData.keys());
                formData.delete('food_image_url[]');
                console.log(formData.keys());
                

                let counter = 0;
                this.inputs.forEach(input=>{
                    let img = document.querySelector('#menu_image_'+input.id).files[0];
                    img = img === undefined ? null : img;
                    if (img) { formData.append('food_image_url['+(counter++)+']', img); }
                    else { formData.append('food_image_url['+(counter++)+']', null); }
                })
                
                this.loading = true;
                axios.post(this.$parent.add_category_action, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    console.log(response);
                    if(response.data.message == "success"){
                        this.$parent.foodAndCategories = response.data.payload;
                        $(this.$refs.addCategoryModal).modal("hide");
                    }
                    this.loading = false;
                }).catch(error => {
                    console.log(error);
                    if (error.response && error.response.status == 422){
                        this.validationErrors = error.response.data.errors;
                    }
                    this.loading = false;
                });
            }
        }
    }
</script>
