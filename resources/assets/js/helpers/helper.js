 /** Get param from query string */
export function getQueryParam(param){
    let url = window.location.search;
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
    return urlParams[param];
}