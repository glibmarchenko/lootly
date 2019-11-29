export const restrictionsMixin = {

  data: {
    form: {
      restrictions: {
        status: 0,
        customer: [],
        product: [],
        activity: [],
        ready: false
      }
    },
    restrictions_options: {
      customer_tags: [],
      vip_tiers: [],
      product_ids: [],
      collections: [],
      currencies: [],
      total_discounts: [],
    },
  },

  methods: {
    getRestrictionsData: function (requestTypes = ['tags', 'tiers']) {
      let typeResponses = {
        tags: (response) => {
          let tags = response.data.data
          this.restrictions_options.customer_tags = tags.map((item) => {
            return item.name
          })
          this.restrictions_options.customer_tag_list = tags.map((item) => {
            return {id: item.id, name: item.name}
          })
        },
        tiers: (response) => {
          let tiers = response.data.data
          this.restrictions_options.vip_tiers = tiers.map((item) => {
            return item.name
          })
          this.restrictions_options.vip_tier_list = tiers.map((item) => {
            return {id: item.id, name: item.name}
          })
        }
      }
      Promise.all(
        requestTypes.map((r) => {
          return axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/' + r)
        })).then(responses => {
        let i = 0
        for (let t of requestTypes) {
          typeResponses[t](responses[i])
          i++
        }
        if (responses[0].data && responses[0].data.data) {
          let tags = responses[0].data.data
          this.restrictions_options.customer_tags = tags.map((item) => {
            return item.name
          })
          this.restrictions_options.customer_tag_list = tags.map((item) => {
            return {id: item.id, name: item.name}
          })
        }
        if (responses[1] && responses[1].data && responses[1].data.data) {
          let tiers = responses[1].data.data
          this.restrictions_options.vip_tiers = tiers.map((item) => {
            return item.name
          })
          this.restrictions_options.vip_tier_list = tiers.map((item) => {
            return {id: item.id, name: item.name}
          })
        }
      }).catch(errors => {
        console.log(errors)
      }).then(() => {
        // OK
      })
    },

    test: function() {
      console.log('test');
    },

    // type can be action or reward
    getRestrictions: function (entityId, type) {

      if( entityId ) {
        axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/' + type + 's/' + entityId + '/restrictions').then((response) => {
          if (response.data && response.data.data) {
            let restrictions = response.data.data

            // Customer restrictions
            let customer_restrictions = restrictions.filter((item) => {
              return item.type === 'customer'
            })
            for (let i = 0; i < customer_restrictions.length; i++) {
              let customer_restriction = customer_restrictions[i]
              for (let j = 0; j < customer_restriction.restrictions.length; j++) {
                if (!customer_restriction.restrictions[j].value.length) {
                  break
                }
                if (!['customer-tags', 'vip-tier'].includes(customer_restriction.restrictions[j].type)) {
                  break
                }
                let options = []
                let values = []
                if (customer_restriction.restrictions[j].type === 'customer-tags') {
                  options = this.restrictions_options.customer_tags
                  values = this.restrictions_options.customer_tag_list.filter((item) => {
                    return customer_restriction.restrictions[j].value.includes(item.id)
                  }).map((item) => {
                    return item.name
                  })
                } else if (customer_restriction.restrictions[j].type === 'vip-tier') {
                  options = this.restrictions_options.vip_tiers
                  values = this.restrictions_options.vip_tier_list.filter((item) => {
                    return customer_restriction.restrictions[j].value.includes(item.id)
                  }).map((item) => {
                    return item.name
                  })
                }

                this.form.restrictions.customer.push(
                  {
                    type: customer_restriction.restrictions[j].type,
                    conditional: customer_restriction.restrictions[j].condition,
                    options: options,
                    values: values
                  }
                )
              }
            }

            // Product restrictions
            let product_restrictions = restrictions.filter((item) => {
              return item.type === 'product'
            })
            for (let i = 0; i < product_restrictions.length; i++) {
              let product_restriction = product_restrictions[i]
              for (let j = 0; j < product_restriction.restrictions.length; j++) {
                if (!product_restriction.restrictions[j].value.length) {
                  break
                }
                if (!['product-id', 'collection'].includes(product_restriction.restrictions[j].type)) {
                  break
                }
                let options = []
                let values = []
                if (product_restriction.restrictions[j].type === 'product-id') {
                  options = this.restrictions_options.product_ids
                  values = product_restriction.restrictions[j].value
                } else if (product_restriction.restrictions[j].type === 'collection') {
                  options = this.restrictions_options.collections
                  values = product_restriction.restrictions[j].value
                }

                this.form.restrictions.product.push(
                  {
                    type: product_restriction.restrictions[j].type,
                    conditional: product_restriction.restrictions[j].condition,
                    options: options,
                    values: values
                  }
                )
              }
            }

            // Activity restrictions
            let activity_restrictions = restrictions.filter((item) => {
              return item.type === 'activity'
            })
            for (let i = 0; i < activity_restrictions.length; i++) {
              let activity_restriction = activity_restrictions[i]
              for (let j = 0; j < activity_restriction.restrictions.length; j++) {
                if (!activity_restriction.restrictions[j].value.length) {
                  break
                }
                if (!['currency', 'total-discounts'].includes(activity_restriction.restrictions[j].type)) {
                  break
                }
                let options = []
                let values = []
                if (activity_restriction.restrictions[j].type === 'currency') {
                  options = this.restrictions_options.currencies
                  values = activity_restriction.restrictions[j].value
                } else if (activity_restriction.restrictions[j].type === 'total-discounts') {
                  options = this.restrictions_options.total_discounts
                  values = activity_restriction.restrictions[j].value
                }

                this.form.restrictions.activity.push(
                  {
                    type: activity_restriction.restrictions[j].type,
                    conditional: activity_restriction.restrictions[j].condition,
                    options: options,
                    values: values
                  }
                )
              }
            }
          }
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          this.form.restrictions.ready = true
        })
      }
      else {
        this.form.restrictions.ready = true
      }
    },
    getTierRestrictions: function (tierId) {
      axios.get('/api/merchants/' + Spark.state.currentTeam.id + '/tiers/' + tierId + '/restrictions').then((response) => {
        if (response.data && response.data.data) {
          let restrictions = response.data.data

          // Customer restrictions
          let customer_restrictions = restrictions.filter((item) => {
            return item.type === 'customer'
          })
          for (let i = 0; i < customer_restrictions.length; i++) {
            let customer_restriction = customer_restrictions[i]
            for (let j = 0; j < customer_restriction.restrictions.length; j++) {
              if (!customer_restriction.restrictions[j].value.length) {
                break
              }
              if (!['customer-tags'].includes(customer_restriction.restrictions[j].type)) {
                break
              }
              if (!['has-any-of', 'is'].includes(customer_restriction.restrictions[j].condition)) {
                break
              }
              let options = []
              let values = []
              if (customer_restriction.restrictions[j].type === 'customer-tags') {
                options = this.restrictions_options.customer_tags
                values = this.restrictions_options.customer_tag_list.filter((item) => {
                  return customer_restriction.restrictions[j].value.includes(item.id)
                }).map((item) => {
                  return item.name
                })
              }

              this.form.restrictions.customer.push(
                {
                  type: customer_restriction.restrictions[j].type,
                  conditional: customer_restriction.restrictions[j].condition,
                  options: options,
                  values: values
                }
              )
            }
          }
        }
      }).catch((error) => {
        console.log(error)
      }).then(() => {
        this.form.restrictions.ready = true
      })
    },
    checkCustomerRestrictionOptions: function (restriction) {
      restriction.values = []
      if (restriction.type === 'vip-tier') {
        restriction.options = this.restrictions_options.vip_tiers
      } else if (restriction.type === 'customer-tags') {
        restriction.options = this.restrictions_options.customer_tags
      }
    },
    checkProductRestrictionOptions: function (restriction) {
      restriction.values = []
      if (restriction.type === 'product-id') {
        restriction.options = this.restrictions_options.product_ids
      } else if (restriction.type === 'collection') {
        restriction.options = this.restrictions_options.collections
      }
    },
    checkActivityRestrictionOptions: function (restriction) {
      restriction.values = []
      if (restriction.type === 'currency') {
        restriction.options = this.restrictions_options.currencies
        restriction.conditional = 'has-any-of'
      } else if (restriction.type === 'total-discounts') {
        restriction.options = this.restrictions_options.total_discounts
        restriction.conditional = 'greater-than'
      }
    },

    // Add tags [Add option] in frontend
    addCustomerRestrictionsTag (newTag, id) {
      this.form.restrictions.customer[id].values.push(newTag)
      if (this.form.restrictions.customer[id].type === 'customer-tags') {
        if (!this.restrictions_options.customer_tags.includes(newTag)) {
          this.restrictions_options.customer_tags.push(newTag)
        }
      } else if (this.form.restrictions.customer[id].type === 'vip-tier') {
        if (!this.restrictions_options.vip_tiers.includes(newTag)) {
          this.restrictions_options.vip_tiers.push(newTag)
        }
      }
    },
    addProductRestrictionsTag (newTag, id) {
      this.form.restrictions.product[id].values.push(newTag)
      if (this.form.restrictions.product[id].type === 'product-id') {
        if (!this.restrictions_options.product_ids.includes(newTag)) {
          this.restrictions_options.product_ids.push(newTag)
        }
      } else if (this.form.restrictions.product[id].type === 'collection') {
        if (!this.restrictions_options.collections.includes(newTag)) {
          this.restrictions_options.collections.push(newTag)
        }
      }
    },
    addActivityRestrictionsTag (newTag, id) {
      this.form.restrictions.activity[id].values.push(newTag)
      if (this.form.restrictions.activity[id].type === 'currency') {
        if (!this.restrictions_options.currencies.includes(newTag)) {
          this.restrictions_options.currencies.push(newTag)
        }
      } else if (this.form.restrictions.activity[id].type === 'total-discounts') {
        if (!this.restrictions_options.total_discounts.includes(newTag)) {
          this.restrictions_options.total_discounts.push(newTag)
        }
      }
    },

    // Clicking "Add" link
    addCustomerRestrictions: function () {
      this.form.restrictions.customer.push(
        {
          type: 'customer-tags',
          conditional: '',
          options: [],
          values: []
        }
      )
    },
    addProductRestrictions: function () {
      this.form.restrictions.product.push(
        {
          type: 'collection',
          conditional: '',
          options: [],
          values: [],
        }
      )
    },
    addActivityRestrictions: function () {
      this.form.restrictions.activity.push(
        {
          type: 'currency',
          conditional: '',
          options: [],
          values: []
        }
      )
    },


    // Clicking "Delete" icon
    deleteCustomerRestriction: function (index) {
      this.form.restrictions.customer.splice(index, 1)
    },
    deleteProductRestriction: function (index) {
      this.form.restrictions.product.splice(index, 1)
    },
    deleteActivityRestriction: function (index) {
      this.form.restrictions.activity.splice(index, 1)
    },


    //Computing allow or not adding new customer restriction
    getAllowCustomerRestrictionAdding: function () {
      let customerTagsRestrictions = this.form.restrictions.customer.filter( function( customer_restriction ) {
        return( customer_restriction['type'] === 'customer-tags' );
      });

      let customerVipRestrictions = this.form.restrictions.customer.filter( function( customer_restriction ) {
        return( customer_restriction['type'] === 'vip-tier' );
      });

      let allow = {
        'add': true,
        'tag': true,
        'vip': true
      };
      if( customerTagsRestrictions.length > 0 ) {
        if( !( customerTagsRestrictions[0].conditional !== 'equals' && customerTagsRestrictions.length < 2 ) ) {
          allow['tag'] = false;
        }
      }
      if( customerVipRestrictions.length > 0 ) {
        if( !( customerVipRestrictions[0].conditional !== 'equals' && customerVipRestrictions.length < 2 ) ) {
          allow['vip'] = false;
        }
      }
      if( !allow['tag'] && !allow['vip'] ) {
        allow['add'] = false;
      }
      return allow;
    },

    //Computing allowed tags for customer restrictions. Used in selects
    getAllowedCustomerConditions: function() {

      let customerRestrictions = this.form.restrictions.customer;
      let conditions = [];

      let firstCustomerTagCondition = true;
      let firstCustomerTagConditionTag = '';
      let lastCustomerTagRestrictionIndex = 5;
      let lastCustomerTagRestrictionPassed = false;

      let firstCustomerVipCondition = true;
      let firstCustomerVipConditionTag = '';
      let lastCustomerVipRestrictionIndex = 5;
      let lastCustomerVipRestrictionPassed = false;

      customerRestrictions.forEach(function(customer_restriction, i, arr) {
        conditions[i] = {
          'has': true,
          'has-none-of': true,
          'equals': true,
          'customerTagShow': true,
          'customerVipShow': true
        };

        //Checking customer restrictions by tags
        if( customer_restriction['type'] === 'customer-tags' ) {

          //If condition after last allowed customer tag condition
          if( lastCustomerTagRestrictionPassed ) {
            conditions[i]['customerTagShow'] = false;
          }

          if( customer_restriction['conditional'] === 'equals' ) {
            lastCustomerTagRestrictionIndex = i;
            lastCustomerTagRestrictionPassed = true;
          }

          //Checking if second customer tag condition (we allow all tags for first condition)
          if( !firstCustomerTagCondition ) {

            //Equals tag is not allowed
            conditions[i]['equals'] = false;

            //If not passed yet last restriction for customer tag
            if( !lastCustomerTagRestrictionPassed ) {

              //Index of current restriction, which can be last
              lastCustomerTagRestrictionIndex = i;
            }

            if( firstCustomerTagConditionTag === 'equals' ) {
              conditions[i]['customerTagShow'] = false;
            }
            if( firstCustomerTagConditionTag === 'has-none-of' ) {
              conditions[i]['has-none-of'] = false;
              lastCustomerTagRestrictionPassed = true;
            }
            if( firstCustomerTagConditionTag === 'has' ) {
              conditions[i]['has'] = false;
              lastCustomerTagRestrictionPassed = true;
            }
          }

          firstCustomerTagConditionTag = customer_restriction['conditional'];
          firstCustomerTagCondition = false;
        }

        //Checking customer restrictions by vip tiers
        if( customer_restriction['type'] === 'vip-tier' ) {

          //If condition after last allowed product collection condition
          if( lastCustomerVipRestrictionPassed ) {
            conditions[i]['collectionShow'] = false;
          }

          if( customer_restriction['conditional'] === 'equals' ) {
            lastCustomerVipRestrictionIndex = i;
            lastCustomerVipRestrictionPassed = true;
          }

          //Checking if second product condition (we allow all tags for first condition)
          if( !firstCustomerVipCondition ) {

            //Equals tag is not allowed
            conditions[i]['equals'] = false;

            //If not passed yet last restriction for product ID
            if( !lastCustomerVipRestrictionPassed ) {

              //Index of current restriction, which can be last
              lastCustomerVipRestrictionIndex = i;
            }

            if( firstCustomerVipConditionTag === 'equals' ) {
              conditions[i]['customerVipShow'] = false;
            }
            if( firstCustomerVipConditionTag === 'has-none-of' ) {
              conditions[i]['has-none-of'] = false;
              lastCustomerVipRestrictionPassed = true;
            }
            if( firstCustomerVipConditionTag === 'has' ) {
              conditions[i]['has'] = false;
              lastCustomerVipRestrictionPassed = true;
            }
          }

          firstCustomerVipConditionTag = customer_restriction['conditional'];
          firstCustomerVipCondition = false;
        }
      });

      return {
        'tags': conditions,
        'lastCustomerTagIndex' : lastCustomerTagRestrictionIndex,
        'lastCustomerVipIndex' : lastCustomerVipRestrictionIndex
      };
    },

    //Computing allow or not adding new product restriction
    getAllowProductRestrictionAdding: function () {
      let productIDsRestrictions = this.form.restrictions.product.filter( function( product_restriction ) {
        return( product_restriction['type'] === 'product-id' );
      });

      let productCollectionsRestrictions = this.form.restrictions.product.filter( function( product_restriction ) {
        return( product_restriction['type'] === 'collection' );
      });

      let allow = {
        'add': true,
        'productId': true,
        'collection': true
      };
      if( productIDsRestrictions.length > 0 ) {
        if( !( productIDsRestrictions[0].conditional !== 'equals' && productIDsRestrictions.length < 2 ) ) {
          allow['productId'] = false;
        }
      }
      if( productCollectionsRestrictions.length > 0 ) {
        if( !( productCollectionsRestrictions[0].conditional !== 'equals' && productCollectionsRestrictions.length < 2 ) ) {
          allow['collection'] = false;
        }
      }
      if( !allow['productId'] && !allow['collection'] ) {
        allow['add'] = false;
      }
      return allow;
    },

    //Computing allowed tags for product restrictions. Used in select
    getAllowedProductConditions: function() {

      let productRestrictions = this.form.restrictions.product;
      let conditions = [];

      let firstProductIdCondition = true;
      let firstProductIdConditionTag = '';
      let lastProductIdRestrictionIndex = 5;
      let lastProductIdRestrictionPassed = false;

      let firstProductCollectionCondition = true;
      let firstProductCollectionConditionTag = '';
      let lastProductCollectionRestrictionIndex = 5;
      let lastProductCollectionRestrictionPassed = false;

      productRestrictions.forEach(function(product_restriction, i, arr) {
        conditions[i] = {
          'has': true,
          'has-none-of': true,
          'equals': true,
          'productIdShow': true,
          'collectionShow': true
        };

        //Checking product restrictions by IDs
        if( product_restriction['type'] === 'product-id' ) {

          //If condition after last allowed product Id condition
          if( lastProductIdRestrictionPassed ) {
            conditions[i]['productIdShow'] = false;
          }

          if( product_restriction['conditional'] === 'equals' ) {
            lastProductIdRestrictionIndex = i;
            lastProductIdRestrictionPassed = true;
          }

          //Checking if second product condition (we allow all tags for first condition)
          if( !firstProductIdCondition ) {

            //Equals tag is not allowed
            conditions[i]['equals'] = false;

            //If not passed yet last restriction for product ID
            if( !lastProductIdRestrictionPassed ) {

              //Index of current restriction, which can be last
              lastProductIdRestrictionIndex = i;
            }

            if( firstProductIdConditionTag === 'equals' ) {
              conditions[i]['productIdShow'] = false;
            }
            if( firstProductIdConditionTag === 'has-none-of' ) {
              conditions[i]['has-none-of'] = false;
              lastProductIdRestrictionPassed = true;
            }
            if( firstProductIdConditionTag === 'has' ) {
              conditions[i]['has'] = false;
              lastProductIdRestrictionPassed = true;
            }
          }

          firstProductIdConditionTag = product_restriction['conditional'];
          firstProductIdCondition = false;
        }

        //Checking product restrictions by collections
        if( product_restriction['type'] === 'collection' ) {

          //If condition after last allowed product collection condition
          if( lastProductCollectionRestrictionPassed ) {
            conditions[i]['collectionShow'] = false;
          }

          if( product_restriction['conditional'] === 'equals' ) {
            lastProductCollectionRestrictionIndex = i;
            lastProductCollectionRestrictionPassed = true;
          }

          //Checking if second product condition (we allow all tags for first condition)
          if( !firstProductCollectionCondition ) {

            //Equals tag is not allowed
            conditions[i]['equals'] = false;

            //If not passed yet last restriction for product ID
            if( !lastProductCollectionRestrictionPassed ) {

              //Index of current restriction, which can be last
              lastProductCollectionRestrictionIndex = i;
            }

            if( firstProductCollectionConditionTag === 'equals' ) {
              conditions[i]['productCollectionShow'] = false;
            }
            if( firstProductCollectionConditionTag === 'has-none-of' ) {
              conditions[i]['has-none-of'] = false;
              lastProductCollectionRestrictionPassed = true;
            }
            if( firstProductCollectionConditionTag === 'has' ) {
              conditions[i]['has'] = false;
              lastProductCollectionRestrictionPassed = true;
            }
          }

          firstProductCollectionConditionTag = product_restriction['conditional'];
          firstProductCollectionCondition = false;
        }

      });

      return {
        'tags': conditions,
        'lastProductIdIndex' : lastProductIdRestrictionIndex,
        'lastProductCollectionIndex' : lastProductCollectionRestrictionIndex
      };
    },

    toggleRestrictions: function () {
      if (this.form.restrictions.status == 0) {
        this.form.restrictions.status = 1
      } else {
        this.form.restrictions.status = 0
      }
    }
  },
  computed: {

    //Computing allow or not adding new customer restriction
    allowCustomerRestrictionAdding: function() {

      return this.getAllowCustomerRestrictionAdding();
    },

    //Computing allowed tags for customer restrictions. Used in select
    allowedCustomerConditions: function() {

      return this.getAllowedCustomerConditions();
    },

    //Computing allow or not adding new product restriction
    allowProductRestrictionAdding: function() {

      return this.getAllowProductRestrictionAdding();
    },

    //Computing allowed tags for product restrictions. Used in select
    allowedProductConditions: function() {

      return this.getAllowedProductConditions();
    }
  }
}
