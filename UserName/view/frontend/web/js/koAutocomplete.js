define(['uiComponent', 'Magento_Catalog/js/product/list/toolbar'], function (Component, toolbar) {
    return Component.extend({
        defaults: {
            searchText: '',
            autocompleteResults: [],
            productName: [],
            minChars: 3
        },
        initObservable: function () {
            this._super();
            this.observe(['searchText', 'autocompleteResults']);
            return this;
        },
        initialize: function () {
            this._super();
            this.searchText.subscribe(this.handleAutocomplete.bind(this));
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

            $.ajax({
                url: 'username/index/index',
                type: 'POST',
                data: JSON.stringify(request),
                contentType: 'application/json',
                success: function(data) {
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
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }
    });
});




