<div class="card mb-3"><div class="card-body">
    <form class="row g-2" method="GET">
        <div class="col-md-3">
            <select name="range" class="form-select" onchange="this.form.submit()">
                <option value="daily" @selected($range==="daily")>Daily (Today)</option>
                <option value="weekly" @selected($range==="weekly")>Weekly</option>
                <option value="monthly" @selected($range==="monthly")>Monthly</option>
                <option value="yearly" @selected($range==="yearly")>Yearly</option>
                <option value="custom" @selected($range==="custom")>Custom Range</option>
            </select>
        </div>
        <div class="col-md-3"><input type="date" name="from" value="{{ request('from') }}" class="form-control" placeholder="From"></div>
        <div class="col-md-3"><input type="date" name="to" value="{{ request('to') }}" class="form-control" placeholder="To"></div>
        <div class="col-md-3"><button class="btn btn-outline-primary w-100">Apply</button></div>
    </form>
    <small class="text-muted">Showing: {{ $from->format("d M Y") }} — {{ $to->format("d M Y") }}</small>
</div></div>
