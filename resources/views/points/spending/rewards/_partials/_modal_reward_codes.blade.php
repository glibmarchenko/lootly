<b-modal class="custom-modal" id="import-coupons" title="Import Reward Codes" hide-footer v-cloak>
    <form action="">
        <div class="row m-b-10 m-t-10">
            <div class="col-md-12">
                <label class="light-font">This tool allows you to import reward codes.</label>
            </div>
        </div>
        <div class="row m-b-10">
            <div class="col-md-12">
                <div class="form-group m-b-5">
                    <button onclick="document.getElementById('couponsFile').click()"
                            class="btn custom-primary btn-primary bold"
                            type="button"
                    >
                        Choose File
                    </button>

                    <label class="m-l-10">
                        <span v-text="importForm.fields.importFileInput && importForm.fields.importFileInput.name ? importForm.fields.importFileInput.name : 'No file chosen'"
                        ></span>
                    </label>

                    <b-form-file v-model="importForm.fields.importFileInput"
                                 name="importFile"
                                 class="d-none"
                                 id="couponsFile"
                                 plain
                                 accept=".csv"
                                 @change="changeFileCoupons"
                    ></b-form-file>
                </div>
            </div>
        </div>
        <div class="row m-t-20 p-b-10 p-t-20 border-top">
            <div class="col-md-6 offset-md-3 text-center">
                <span v-if="form.reward_id">
                    <span v-if="importForm.saving" class="i-loading"></span>
                    <button v-show="!importForm.saving" 
                            :disabled="!importForm.fields.importFileInput"
                            @click.prevent="importCoupons" 
                            class="btn modal-btn-lg btn-block btn-success btn-glow">
                        Add Codes
                    </button>                            
                </span>
                <button v-else
                        @click.prevent="hideModalImportCoupons"
                        class="btn modal-btn-lg btn-block btn-success btn-glow"
                >
                    OK
                </button>
            </div>
        </div>
    </form>
</b-modal>
