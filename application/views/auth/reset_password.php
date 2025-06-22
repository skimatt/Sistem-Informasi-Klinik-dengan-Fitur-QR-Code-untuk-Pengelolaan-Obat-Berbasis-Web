<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
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
        <h2>Reset Password</h2>

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

        <?php if (isset($msg) && !empty($msg)): ?>
             <div class="alert alert-info">
                <?php echo $msg; ?>
             </div>
        <?php endif; ?>

        <?php if (isset($error) && !empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php echo form_open('auth/reset_password/' . $token); ?>
            <input type="hidden" name="token" value="<?php echo $token; ?>">

            <label for="password">Password Baru:</label><br>
            <input type="password" id="password" name="password"><br>
            <?php echo form_error('password', '<div class="error-message">', '</div>'); ?><br>

            <label for="passconf">Konfirmasi Password:</label><br>
            <input type="password" id="passconf" name="passconf"><br>
            <?php echo form_error('passconf', '<div class="error-message">', '</div>'); ?><br>

            <button type="submit">Reset Password</button>
        <?php echo form_close(); ?>
    </div>
</body>
</html>