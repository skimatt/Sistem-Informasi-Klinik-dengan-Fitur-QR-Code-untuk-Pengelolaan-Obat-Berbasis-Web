<!DOCTYPE html>
<html>
<head>
    <title>Lupa Password</title>
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
        <h2>Lupa Password</h2>

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

        <?php echo form_open('auth/forgot_password'); ?>
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" value="<?php echo set_value('email'); ?>"><br>
            <?php echo form_error('email', '<div class="error-message">', '</div>'); ?><br>

            <button type="submit">Kirim Link Reset</button>
        <?php echo form_close(); ?>

        <p><a href="<?php echo site_url('auth/login'); ?>">Kembali ke Login</a></p>
    </div>
</body>
</html>