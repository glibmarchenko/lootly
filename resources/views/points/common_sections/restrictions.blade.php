<div class="row section-border-bottom p-b-15 m-b-15">
    <div class="col-md-8">
        <p class="bolder f-s-15 m-b-10 m-t-0">{{ $type }} Restrictions</p>
        <label class="light-font m-b-0">
            {{ $type }} restrictions are
            <span class="bold"
                  v-text="form.restrictions.status == 0 ? 'Disabled' : 'Enabled'"></span>
        </label>
    </div>
    <div class="col-md-4 text-right">
        <a @click="toggleRestrictions" v-cloak>
            <span v-if="form.restrictions.status == 0">
                <span class="badge custom-badge badge-md badge-success m-l-20 p-l-20 p-r-20">Enable</span>
            </span>
            <span v-else>
                <span class="badge custom-badge badge-md badge-danger m-l-20 p-l-20 p-r-20">Disable</span>
            </span>
        </a>
    </div>
</div>

@if( in_array('customer', $restrictions ) )
    <div class="{{ end( $restrictions ) != 'customer' ? 'border-bottom' : '' }} p-b-15">
        <div class="row m-b-5">
            <div class="col-md-12">
                <label class="m-b-5 pull-left">Customer Restrictions</label>
                <a class="color-blue bolder f-s-14 pull-right" v-if="allowCustomerRestrictionAdding['add']"
                   @click="addCustomerRestrictions">Add</a>
            </div>
        </div>
        <div class="row select-inline-boxes m-b-10"
             v-for="(customerRestriction, index) in form.restrictions.customer" v-if="allowedCustomerConditions.tags[index]['customerTagShow']" v-cloak>
            <div class="col-md-12 col-12">
                <select class="form-control custom-select"
                        v-model="customerRestriction.type"
                        @change="checkCustomerRestrictionOptions(customerRestriction)">
                    <option value="customer-tags" v-if="allowCustomerRestrictionAdding['tag'] || index <= allowedCustomerConditions['lastCustomerTagIndex']">Customer tags</option>
                    <option value="vip-tier" v-if="allowCustomerRestrictionAdding['vip'] || index <= allowedCustomerConditions['lastCustomerVipIndex']">VIP Tier</option>
                </select>
                <select class="form-control custom-select"
                        v-model="customerRestriction.conditional">
                    <option value="" disabled="">Select one</option>
                    <option value="has" v-if="allowedCustomerConditions.tags[index]['has']">has</option>
                    <option value="has-none-of" v-if="allowedCustomerConditions.tags[index]['has-none-of']">has none of</option>
                    <option value="equals" v-if="allowedCustomerConditions.tags[index]['equals']">equals</option>
                </select>
                <multiselect
                        v-model="customerRestriction.values"
                        tag-placeholder="Add"
                        placeholder="Add option"
                        select-label="Select"
                        deselect-label="Remove"
                        open-direction="bottom"
                        :id="index"
                        :options="customerRestriction.options"
                        multiple="true"
                        :taggable="true"
                        class="responsive"
                        :class="[customerRestriction.conditional === 'equals' ? 'single' : 'multiple', customerRestriction.values.length > 0 ? 'has-tags' : '']"
                        @tag="addCustomerRestrictionsTag">
                </multiselect>
                <button class="btn btn-default pull-right" type="button"
                        @click="deleteCustomerRestriction(index)">
                    <i class="fa fa-trash-o f-s-19"></i>
                </button>
            </div>
        </div>
    </div>
@endif

@if( in_array('product', $restrictions ) )
    <div class="{{ end( $restrictions ) != 'product' ? 'border-bottom' : '' }} m-t-15 p-b-15">
        <div class="row m-b-5">
            <div class="col-md-12">
                <label class="m-b-5 pull-left">Product Restrictions</label>
                <a class="color-blue bolder f-s-14 pull-right" v-if="allowProductRestrictionAdding['add']"
                   @click="addProductRestrictions">Add</a>
            </div>
        </div>
        <div class="row select-inline-boxes m-b-10"
             v-for="(productRestriction, index) in form.restrictions.product" v-if="allowedProductConditions.tags[index]['productIdShow']" v-cloak>
            <div class="col-md-12 col-12">

                <select class="form-control custom-select"
                        v-model="productRestriction.type"
                        @change="checkProductRestrictionOptions(productRestriction)">
                    <option value="product-id" v-if="allowProductRestrictionAdding['productId'] || index <= allowedProductConditions['lastProductIdIndex']">Product ID</option>
                    <option value="collection" v-if="allowProductRestrictionAdding['collection'] || index <= allowedProductConditions['lastProductCollectionIndex']">Collection</option>
                </select>
                <select class="form-control custom-select"
                        v-model="productRestriction.conditional">
                    <option value="" disabled="">Select one</option>
                    <option value="has" v-if="allowedProductConditions.tags[index]['has']">has</option>
                    <option value="has-none-of" v-if="allowedProductConditions.tags[index]['has-none-of']">has none of</option>
                    <option value="equals" v-if="allowedProductConditions.tags[index]['equals']">equals</option>
                </select>
                <multiselect
                        v-model="productRestriction.values"
                        tag-placeholder="Add"
                        placeholder="Add option"
                        select-label="Select"
                        deselect-label="Remove"
                        open-direction="bottom"
                        :id="index"
                        :options="productRestriction.options"
                        multiple="true"
                        :taggable="true"
                        class="responsive"
                        :class="[productRestriction.conditional === 'equals' ? 'single' : 'multiple', productRestriction.values.length > 0 ? 'has-tags' : '']"
                        @tag="addProductRestrictionsTag">
                </multiselect>
                <button class="btn btn-default pull-right" type="button"
                        @click="deleteProductRestriction(index)">
                    <i class="fa fa-trash-o f-s-19"></i>
                </button>
            </div>
        </div>
    </div>
@endif

@if( in_array('activity', $restrictions ) )
    <div class="m-t-15 p-b-5">
        <div class="row m-b-5">
            <div class="col-md-12">
                <label class="m-b-5 pull-left">Activity Restrictions</label>
                <a class="color-blue bolder f-s-14 pull-right"
                   @click="addActivityRestrictions">Add</a>
            </div>
        </div>
        <div class="row select-inline-boxes m-b-10"
             v-for="(activityRestriction, index) in form.restrictions.activity" v-cloak>
            <div class="col-md-12 col-12">

                <select class="form-control custom-select"
                        v-model="activityRestriction.type"
                        @change="checkActivityRestrictionOptions(activityRestriction)">
                    <option value="currency">Currency</option>
                    <option value="total-discounts">Total discounts</option>
                </select>

                <select class="form-control custom-select"
                        v-model="activityRestriction.conditional"
                        v-if="activityRestriction.type == 'currency'">
                    <option value="" disabled="">Select one</option>
                    <option value="has">has</option>
                    <option value="has-none-of">has none of</option>
                    <option value="equals">equals</option>
                </select>
                <select class="form-control custom-select"
                        v-model="activityRestriction.conditional"
                        v-else>
                    <option value="greater-than">greater than</option>
                    <option value="equals">equals</option>
                </select>
                <span v-if="activityRestriction.type == 'currency'">
                    <multiselect
                            v-model="activityRestriction.values"
                            tag-placeholder="Add"
                            placeholder="Add option"
                            select-label="Select"
                            deselect-label="Remove"
                            open-direction="bottom"
                            :id="index"
                            :options="activityRestriction.options"
                            multiple="true"
                            :taggable="true"
                            class="responsive"
                            :class="[activityRestriction.conditional === 'equals' ? 'single' : 'multiple', activityRestriction.values.length > 0 ? 'has-tags' : '']"
                            @tag="addActivityRestrictionsTag">
                    </multiselect>
                </span>
                <input class="form-control" v-model="activityRestriction.values" v-else>

                <button class="btn btn-default pull-right" type="button"
                        @click="deleteActivityRestriction(index)">
                    <i class="fa fa-trash-o f-s-19"></i>
                </button>
            </div>
        </div>
    </div>
@endif
