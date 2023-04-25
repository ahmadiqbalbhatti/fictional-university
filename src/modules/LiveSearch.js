import $ from 'jquery'

class LiveSearch {

    // 1. Where we describe and initiate  our object
    constructor() {
        this.addSearchHTML();

        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
        this.searchField = $('#search-term');
        this.resultsDiv = $("#search-overlay__results");
        this.events();
        this.typingTimer;
        this.previousValue;
        this.isOverlayOpen = false;
        this.isSpinnerVisible = false;


    }

    // 2. Events


    events() {
        this.openButton.on('click', this.openOverlay.bind(this));
        this.closeButton.on('click', this.closeOverlay.bind(this));
        $(document).on("keydown", this.keyPressDispatcher.bind(this));

        this.searchField.on('keyup', this.typingLogic.bind(this))

    }

    // 3. Methods (function, action,...)

    openOverlay() {
        this.searchOverlay.addClass("search-overlay--active");
        $("body").addClass("body-no-scroll");
        this.searchField.val('')
        setTimeout(() => this.searchField.focus(), 301)
        this.isOverlayOpen = true;

        return false;
    }

    closeOverlay() {
        this.searchOverlay.removeClass("search-overlay--active");
        $("body").removeClass("body-no-scroll");
        this.isOverlayOpen = false;
    }

    keyPressDispatcher(e) {
        if (e.keyCode === 83 && !this.isOverlayOpen && !$("input, textarea").is(":focus")) {
            this.openOverlay();
        }
        if (e.keyCode === 27 && this.isOverlayOpen) {
            this.closeOverlay();
        }
    }

    typingLogic() {
        if (this.searchField.val() !== this.previousValue) {
            // console.log(this.searchField.val());
            clearTimeout(this.typingTimer);
            if (this.searchField.val()) {
                if (!this.isSpinnerVisible) {
                    this.resultsDiv.html('<div class="spinner-loader"> </div>');
                    this.isSpinnerVisible = true;
                }
                this.typingTimer = setTimeout(this.getResults.bind(this), 750);
            } else {
                this.resultsDiv.html('');
                this.isSpinnerVisible = false;
            }

        }
        this.previousValue = this.searchField.val();
    }

    showResults(results) {

        // if (results.length){
        //     return `
        //     <ul class="link-list min-list">
        //     ${results.map(result => `<li><a href="/${result.permalink}">
        //             ${result.title}</a> ${result.postType === 'post' ? `Post published by ${result.authorName}` :
        //         ''} </li>`).join('')}
        //     </ul>
        //     `
        // }
        // return `
        // <p>No Information Matches the search Query! </p>
        // `

        return `
        ${results.length ? '<ul class="link-list min-list">' :
            `<p>No Information Matches the search Query!</p>`}
                    ${results.map(result => `<li><a href="/${result.permalink}">
                    ${result.title}</a> ${result.postType === 'post' ? `Post published by ${result.authorName}` :
            ''} </li>`).join('')}
                    ${results.length ? '</ul>' : ``}
        `

    }

    getResults() {

        $.getJSON(universityData.root_url + '/wp-json/university/v1/search?term=' + this.searchField.val(), results => {
            // console.log(results )
            this.resultsDiv.html(`
            <div class = "row">
                <div class="one-third">
                    <h2 class="search-overlay__section-title">General Information</h2>
                    ${this.showResults(results.generalInfo)}

                </div>
                <div class="one-third">
<!--                    Programs -->
                    <h2 class="search-overlay__section-title">Programs</h2>
                        ${this.showResults(results.programs)}
                        
<!--                    Professors -->
                    <h2 class="search-overlay__section-title">Professors</h2>
                        ${results.professors.length ? '<ul class="professor-cards">' : '<p>No' +
                ' professors match the search result!</p>'}
                        ${results.professors.map(item => `
                        <li class="professor-card__list-item">
                        <a class="professor-card" href="${item.permalink}">
                            <img src="${item.image}"
                                 class="professor-card__image"/>
                            <span class="professor-card__name">${item.title}</span>
                        </a>
                    </li>
                        `)}
                        ${results.length ? '</ul>' : ``}
                </div>
                <div class="one-third">
                    <h2 class="search-overlay__section-title">Campuses</h2>
                        ${this.showResults(results.campuses)}
                        
<!--                        Events-->
                    <h2 class="search-overlay__section-title">Events</h2>
                        ${results.events.length ? '' : '<p>No events match the search result!</p>'}
                        ${results.events.map(item => `
                                <div class="event-summary">
                                    <a class="event-summary__date t-center" href="#">
                                                        <span class="event-summary__month">${item.month}</span>
                                        <span class="event-summary__day">${item.day}</span>
                                    </a>
                                    <div class="event-summary__content">
                                        <h5 class="event-summary__title headline headline--tiny"><a
                                                href="${item.permalink}">${item.title}</a></h5>
                                        <p>${item.description} <a href="${item.permalink}" class="nu gray">Learn
                                                more</a></p>
                                    </div>
                                </div>
                        `)}
                </div>
            </div>
            `)

        })

        // // Delete the asynchronous part of code when the new and customized api is applied
        // $.when($.getJSON(universityData.root_url + '/wp-json/university/v1/search?term' + this.searchField.val()), $.getJSON(universityData.root_url + '/wp-json/wp/v2/pages?search=' + this.searchField.val()),).then((posts, pages) => {
        //     let results = posts[0].concat(pages[0]);
        //     this.resultsDiv.html(`
        //         <h2 class="search-overlay__section-title"> General Information </h2>
        //         ${results.length ? '<ul class="link-list min-list">' : '<p>No General Information' + ' Maches the search Query! </p>'}
        //             ${results.map(result => `<li><a href="/${result.slug}">${result.title.rendered}</a> ${result.type === 'post' ? `Published by ${result.authorName}` : ''} </li>`).join('')}
        //         ${results.length ? '</ul>' : ''}
        //     `);
        //     this.isSpinnerVisible = false;
        // }, () => {
        //     this.resultsDiv.html('<p>Unexpected error; Please try again!</p>')
        // })


        // $.getJSON(universityData.root_url + '/wp-json/wp/v2/posts?search=' + this.searchField.val(), (posts) => {
        //     // alert(posts[0].title.rendered);
        //     $.getJSON(universityData.root_url + '/wp-json/wp/v2/pages?search=' + this.searchField.val(), pages => {
        //         let results = posts.concat(pages);
        //         this.resultsDiv.html(`
        //         <h2 class="search-overlay__section-title"> General Information </h2>
        //         ${results.length ? '<ul class="link-list min-list">' : '<p>No General Information' +
        //             ' Maches the search Query! </p>'}
        //             ${results.map(result => `<li><a href="/${result.slug}">${result.title.rendered}</a> </li>`).join('')}
        //         ${results.length ? '</ul>' : ''}
        //     `);
        //         this.isSpinnerVisible = false;
        //     })
        // })
        // // this.resultsDiv.html("Imaging Really searches here");
        // // this.isSpinnerVisible = false;
    }

    addSearchHTML() {
        $("body").append(`
        <div class="search-overlay">
            <div class="search-overlay__top">
                <div class="container">
                     <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                     <input type="text" class="search-term" autocomplete="off"
                   placeholder="What are you looking for?" id="search-term"/>
                      <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
                </div>
            </div>

            <div class="container">
                <div id="search-overlay__results">
                </div>
            </div>
        </div>
    `)
    }
}

export default LiveSearch;