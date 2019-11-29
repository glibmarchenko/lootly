export const rewardCodesMixin = {
    data: {
        rewardCoupons: {
            available: {
                data: 'No available codes.',
                loading: false
            },
            all: {
                data: 'No codes.',
                loading: false
            },
            loading: true
        },
        importForm: {
            data: {
                importFile: null,
            },
            fields: {
                importFileInput: null,
            },
            saving: false
        },
    },
    methods: {
        showRewardCodes: function () {
            let merchantSettings = this.merchantSettings;

            if (merchantSettings.integrations) {
                for (let prop in merchantSettings.integrations) {
                    if (merchantSettings.integrations[prop]['is_api'] === true) {
                        return true;
                    }
                }
            }

            return false;
        },
        getRewardCoupons: function () {
            if (!this.form.reward_id) {
                return false;
            }
            this.rewardCoupons.loading = true;
            axios.get('/points/spending/rewards/get-coupons/' + this.form.reward_id).then((response) => {
                if (response.data) {
                    let data = response.data;

                    if (data.all.length !== 0) {
                        this.rewardCoupons.all.data = data.all.map((item) => {
                            return {
                                code: item.code,
                                status: item.status,
                                date: item.created_at,
                            }
                        });
                    }

                    if (data.available.length !== 0) {
                        this.rewardCoupons.available.data = data.available.map((item) => {
                            return {
                                code: item.code,
                                date: item.created_at,
                            }
                        });
                    }
                }
                this.rewardCoupons.loading = false;                
            })
        },
        importCoupons: function () {
            const comp = this;

            if (!comp.importForm.saving) {
                comp.importForm.saving = true;

                const fileUpload = new Promise(function (resolve, reject) {
                    if (comp.importForm.fields.importFileInput) {
                        let file = comp.importForm.fields.importFileInput;
                        let reader = new FileReader();
                        reader.onload = function (e) {
                            comp.importForm.data.importFile = e.target.result;
                            resolve()
                        };
                        reader.readAsDataURL(file);
                    } else {
                        reject()
                    }
                });

                fileUpload.then((response) => {
                    axios.post('/points/spending/rewards/import-coupons/' + comp.form.reward_id, comp.importForm.data).then((response) => {
                        comp.getRewardCoupons();

                        comp.importForm.data.importFile = null;
                        comp.importForm.fields.importFileInput = null;

                        document.getElementById('couponsFile').value = '';

                        comp.$root.$emit('bv::hide::modal', 'import-coupons');
                        comp.importForm.saving = false;
                        comp.alert.type = 'success';
                        comp.alert.text = 'Reward codes successfully imported';
                    }).then(() => {
                        comp.alert.dismissCountDown = comp.alert.dismissSecs;

                    }).catch((error) => {
                        clearErrors(comp.$el);
                        showErrors(comp.$el, error.response.data.errors);

                        comp.alert.type = 'danger';
                        comp.alert.text = error.response.data.message;
                    });

                }).catch((error) => {
                    comp.importForm.saving = false;                    
                    clearErrors(comp.$el);
                    showErrors(comp.$el, {
                        'importFile': [
                            'No file chosen'
                        ]
                    });
                })
            }
        },
        setFormReward: function (id) {
            const copyForm = Object.assign({}, this.form);

            this.form.reward_id = id;

            if (! copyForm.reward_id) {
                this.importCoupons();
            }
        },
        openModel: function (modelName) {
            this.$root.$emit('bv::show::modal', modelName);
        },
        closeModel: function (modelName) {
            this.$root.$emit('bv::hide::modal', modelName);
        },
        showModalImportCoupons: function () {
            this.importForm.saving = false;
            this.openModel('import-coupons');
        },
        hideModalImportCoupons: function () {
            this.closeModel('import-coupons');
        },
        changeFileCoupons: function (event) {
            const files = event.target.files;

            let fileName = '';

            for (let prop in files) {
                if (files[prop].name) {
                    fileName += files[prop].name;
                }
                break;
            }

            if (fileName && typeof this.rewardCoupons.all.data === 'string') {
                this.rewardCoupons.all.data = `Selected "${fileName}" file`;
            }

            if (fileName && typeof this.rewardCoupons.available.data === 'string') {
                this.rewardCoupons.available.data = `Selected "${fileName}" file`;
            }

            this.importForm.saving = false;
        },
    },
};
