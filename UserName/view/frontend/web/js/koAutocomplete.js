define(['uiComponent', 'Magento_Catalog/js/product/list/toolbar'], function (Component, toolbar) {
    return Component.extend({
        defaults: {
            searchText: '',
            autocompleteResults: [],
            productName: '',
            minChars: 3
        },
        initObservable: function () {
            this._super();
            this.observe(['searchText', 'autocompleteResults', 'productName']);
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

            $.ajax({
                url: 'username/index/addtocart',
                type: 'POST',
                data: { sku: this.searchText },
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    self.autocompleteResults(data.results);
                    self.productName(data.productName);
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }
    });
});




