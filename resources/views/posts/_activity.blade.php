<div class="container">
    <div class="row mt-4">
        @card(['title' => 'Most Commented'])
            @slot('subtitle')
                What people are currently talking about
            @endslot
            @slot('items', collect($mostCommented->pluck('title')))
        @endcard
    </div>

    <div class="row mt-4">
        @card(['title' => 'Most Active'])
            @slot('subtitle')
                Users with most posts written
            @endslot
            @slot('items', collect($mostActive->pluck('name')))
        @endcard
    </div>

    <div class="row mt-4">
        @card(['title' => 'Most Active Last Month'])
            @slot('subtitle')
                Users with most posts written in the month
            @endslot
            @slot('items', collect($mostActiveLastMonth->pluck('name')))
        @endcard
    </div>
</div>