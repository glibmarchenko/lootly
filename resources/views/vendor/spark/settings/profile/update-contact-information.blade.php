<spark-update-contact-information :user="user" inline-template>
    <div class="card card-default">
        <div class="card-header">{{__('Contact Information')}}</div>

        <div class="card-body">
            <!-- Success Message -->
            <div class="alert alert-success" v-if="form.successful">
                {{__('Your contact information has been updated!')}}
            </div>

            <form role="form">
                <!-- Name -->
                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">{{__('First Name')}}</label>

                    <div class="col-md-6">
                        <input type="text" class="form-control" name="first_name" v-model="form.first_name" :class="{'is-invalid': form.errors.has('first_name')}">

                        <span class="invalid-feedback" v-show="form.errors.has('first_name')">
                            @{{ form.errors.get('first_name') }}
                        </span>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">{{__('Last Name')}}</label>

                    <div class="col-md-6">
                        <input type="text" class="form-control" name="last_name" v-model="form.last_name" :class="{'is-invalid': form.errors.has('last_name')}">

                        <span class="invalid-feedback" v-show="form.errors.has('last_name')">
                            @{{ form.errors.get('last_name') }}
                        </span>
                    </div>
                </div>

                <!-- E-Mail Address -->
                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">{{__('E-Mail Address')}}</label>

                    <div class="col-md-6">
                        <input type="email" class="form-control" name="email" v-model="form.email" :class="{'is-invalid': form.errors.has('email')}">

                        <span class="invalid-feedback" v-show="form.errors.has('email')">
                            @{{ form.errors.get('email') }}
                        </span>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">{{__('Billing Email Address')}}</label>

                    <div class="col-md-6">
                        <input type="billing_email" class="form-control" name="billing_email" v-model="form.billing_email" :class="{'is-invalid': form.errors.has('billing_email')}">

                        <span class="invalid-feedback" v-show="form.errors.has('billing_email')">
                            @{{ form.errors.get('billing_email') }}
                        </span>
                    </div>
                </div>

                <!-- Update Button -->
                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary"
                                @click.prevent="update"
                                :disabled="form.busy">

                            {{__('Update')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</spark-update-contact-information>
