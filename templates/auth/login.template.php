<div class="container">
    <h5>Please log in</h5>
	<form method="post" action="/login">
		<div class="form-group row">
			<label for="input-username" class="col-form-label col-sm-2">Username</label>
			<div class="col-sm-10">
				<input type="text" id="input-username" class="form-control" name="username" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="input-password" class="col-form-label col-sm-2">Password</label>
			<div class="col-sm-10">
				<input type="password" id="input-password" class="form-control" name="password" required>
			</div>
		</div>
		<div class="form-group row">
			<div class="offset-sm-2 col-sm-10">
				<button type="submit" class="btn btn-primary">Sign in</button>
			</div>
		</div>
	</form>
</div>