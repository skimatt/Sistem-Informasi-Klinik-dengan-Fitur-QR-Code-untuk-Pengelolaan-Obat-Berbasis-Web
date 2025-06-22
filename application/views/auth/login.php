<!DOCTYPE html>
<html>
<head>
    <title>Login Apotek</title>
    <style>
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .success-message {
            color: green;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login Sistem Apotek</h2>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger">
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success">
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error) && !empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php echo form_open('auth/login'); ?>
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" value="<?php echo set_value('username'); ?>"><br>
            <?php echo form_error('username', '<div class="error-message">', '</div>'); ?><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password"><br>
            <?php echo form_error('password', '<div class="error-message">', '</div>'); ?><br>

            <button type="submit">Login</button>
        <?php echo form_close(); ?>

        <p><a href="<?php echo site_url('auth/forgot_password'); ?>">Lupa Password?</a></p>
    </div>
</body>
</html>