<template>
    <div>
        <a v-if="isEdit" href="/points/earning" class="bold f-s-15 color-blue">
            <i class="arrow left blue"></i>
            <span class="m-l-5">View Earning Actions</span>
        </a>
        <a v-if="!isEdit" href="/points/earning/actions" class="bold f-s-15 color-blue">
            <i class="arrow left blue"></i>
            <span class="m-l-5">Add Earning Action</span>
        </a>
    </div>
</template>

<script>
  export default {
    data: function () {
      return {
        isEdit: false
      }
    },
    created: function () {
      this.isEdit = this.getQueryParam('edit') === '1'
    },
    methods: {
      getQueryParam: function (param) {
        let url = window.location.search
        url = url.split('#')[0] // Discard fragment identifier.
        const urlParams = {}
        let queryString = url.split('?')[1]
        if (!queryString) {
          if (url.search('=') !== false) {
            queryString = url
          }
        }
        if (queryString) {
          let keyValuePairs = queryString.split('&')
          for (let i = 0; i < keyValuePairs.length; i++) {
            let keyValuePair = keyValuePairs[i].split('=')
            let paramName = keyValuePair[0]
            let paramValue = keyValuePair[1] || ''
            urlParams[paramName] = decodeURIComponent(paramValue.replace(/\+/g, ' '))
          }
        }
        return urlParams[param]
      }
    }
  }
</script>
