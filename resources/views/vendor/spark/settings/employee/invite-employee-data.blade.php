<invite-employee-data  inline-template>


    <div class="employee employee-default">
        <!-- Update Payment Method Heading -->
        <div class="employee-header">


            <div class="clearfix"></div>
        </div>

        <div class="employee-body">
            <div class="alert alert-success" v-if="form.successful">
                {{__('Invite Employee has been updated!')}}
            </div>

            <form role="form">

                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{__('First name')}}</label>

                    <div class="col-md-6">
                        <input type="text" class="form-control"  v-model="form.first_name" :class="{'is-invalid': form.errors.has('first_name')}">
                        <span class="invalid-feedback" v-show="form.errors.has('first_name')">
                            @{{ form.errors.get('first_name') }}
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{__('Last name')}}</label>

                    <div class="col-md-6">
                        <input type="text" class="form-control" v-model="form.last_name" :class="{'is-invalid': form.errors.has('last_name')}">
                        <span class="invalid-feedback" v-show="form.errors.has('last_name')">
                            @{{ form.errors.get('last_name') }}
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{__('email')}}</label>

                    <div class="col-md-6">
                        <input type="email" class="form-control" v-model="form.email" :class="{'is-invalid': form.errors.has('email')}">
                        <span class="invalid-feedback" v-show="form.errors.has('email')">
                            @{{ form.errors.get('email') }}
                        </span>
                    </div>

                </div>


                <!-- Update Button -->
                <div class="form-group row">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary" @click.prevent="invite">
                            <span v-if="form.busy">
                                <i class="fa fa-btn fa-spinner fa-spin"></i>
                            {{__('Invite')}}
                            </span>

                            <span v-else>
                                {{__('Invate')}}
                            </span>
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</invite-employee-data>
