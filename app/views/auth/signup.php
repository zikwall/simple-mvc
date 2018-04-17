<!-- Page Content -->
<div class="bg-image" style="background-image: url('/public/assets/img/photos/photo34@2x.jpg');">
    <div class="row mx-0 bg-earth-op">
        <div class="hero-static col-md-6 col-xl-8 d-none d-md-flex align-items-md-end">
            <div class="p-30 invisible" data-toggle="appear">
                <p class="font-size-h3 font-w600 text-white mb-5">
                    We're very happy you are joining our community!
                </p>
                <p class="font-size-h5 text-white">
                    <i class="fa fa-angles-right"></i> Create your account today and receive 50% off.
                </p>
                <p class="font-italic text-white-op">
                    Copyright &copy; <span class="js-year-copy">2017</span>
                </p>
            </div>
        </div>
        <div class="hero-static col-md-6 col-xl-4 d-flex align-items-center bg-white">
            <div class="content content-full">
                <!-- Header -->
                <div class="px-30 py-10">
                    <a class="link-effect font-w700" href="/">
                        <i class="si si-fire"></i>
                        <span class="font-size-xl text-primary-dark">cell</span><span class="font-size-xl">brush</span>
                    </a>
                    <h1 class="h3 font-w700 mt-30 mb-10">Create New Account</h1>
                    <h2 class="h5 font-w400 text-muted mb-0">Please add your details</h2>
                </div>
                <!-- END Header -->

                <!-- Sign Up Form -->
                <!-- jQuery Validation (.js-validation-signup class is initialized in js/pages/op_auth_signup.js) -->
                <!-- For more examples you can check out https://github.com/jzaefferer/jquery-validation -->
                <form class="js-validation-signup px-30 form-signin ajax" action="/auth/ajax" method="post">
                    <div class="main-error alert alert-error"></div>
                    <div class="form-group row">
                        <div class="col-12">
                            <div class="form-material floating">
                                <input type="text" class="form-control" id="username" name="username">
                                <label for="signup-username">Username</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <div class="form-material floating">
                                <input type="email" class="form-control" id="email" name="email">
                                <label for="signup-email">Email</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <div class="form-material floating">
                                <input type="password" class="form-control" id="password1" name="password1">
                                <label for="signup-password">Password</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <div class="form-material floating">
                                <input type="password" class="form-control" id="password2" name="password2">
                                <label for="signup-password-confirm">Password Confirmation</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="signup-terms" name="signup-terms">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">I agree to Terms &amp; Conditions</span>
                            </label>
                        </div>
                    </div>
                    <input type="hidden" name="act" value="register">
                    <div class="form-group">
                        <button type="submit" class="btn btn-sm btn-hero btn-alt-success">
                            <i class="fa fa-plus mr-10"></i> Create Account
                        </button>
                        <div class="mt-30">
                            <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="#" data-toggle="modal" data-target="#modal-terms">
                                <i class="fa fa-book text-muted mr-5"></i> Read Terms
                            </a>
                            <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="/auth/login">
                                <i class="fa fa-user text-muted mr-5"></i> Sign In
                            </a>
                        </div>
                    </div>
                </form>
                <!-- END Sign Up Form -->
            </div>
        </div>
    </div>
</div>
<!-- END Page Content -->
