define(['uiComponent', 'Magento_Catalog/js/product/list/toolbar'], function (Component, toolbar) {
    return Component.extend({
        defaults: {
            searchText: '',
            autocompleteResults: ko.observableArray([]),
            productName: ko.observable(''),
            minChars: 3
        },
        initialize: function () {
            this._super();
            this.autocompleteResults([]);
        },
        handleAutocomplete: function (searchText) {
            var self = this;
            self.searchText = searchText;

            if (searchText.length < self.minChars) {
                self.autocompleteResults([]);
                self.productName('');
                return;
            }

            var filters = {
                'sku': {
                    'like': '%' + searchText + '%'
                }
            };

            var request = toolbar().getRequest();
            request['filter_groups'] = [{
                'filters': [filters]
            }];

            fetch('username/index/index', {
                method: 'POST',
                body: JSON.stringify(request),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    var results = [];
                    if (data.items && data.items.length) {
                        data.items.forEach(function(item) {
                            results.push({
                                sku: item.sku,
                                name: item.name
                            });
                        });
                    }
                    self.autocompleteResults(results);
                })
                .catch(function(error) {
                    console.error('Error:', error);
                });
        }
    });
});




