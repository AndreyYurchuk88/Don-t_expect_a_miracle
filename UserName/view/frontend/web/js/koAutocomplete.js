define(['uiComponent', 'jquery'], function (Component, $) {
    return Component.extend({
        defaults: {
            searchText: '',
            searchResult: [],
            minChars: 3
        },
        //устанавливаем наблюдение за searchText и searchResult
        initObservable: function () {
            this._super();
            this.observe(['searchText', 'searchResult']);
            return this;
        },
        //при изменении значения searchText - вызывается handleAutocomplete
        initialize: function () {
            this._super();
            this.searchText.subscribe(this.handleAutocomplete.bind(this));
        },
        handleAutocomplete: function (searchValue) {
            if (searchValue.length >= this.minChars) {
                var self = this;
                $.ajax({
                    url: 'username/index/searchsku',
                    data: { search_text: searchValue },
                    type: 'POST',
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        self.searchResult(response);
                    }
                });
            } else {
                this.searchResult([]);
            }
        }
    });
});



