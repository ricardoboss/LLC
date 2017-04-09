@section("content")
<div class="container">
    <div class="jumbotron">
        <h1>Welcome</h1>
        <hr>
        <h4>...to the template page</h4>
    </div>

    @include("auth.login")
    <hr>
    @include("auth.register")
</div>
@endSection

@section("title")Home@endSection

@include("app.master")