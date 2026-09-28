<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }

        /* Base */

        body,
        body *:not(html):not(style):not(br):not(tr):not(code) {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif,
            'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            position: relative;
        }

        body {
            -webkit-text-size-adjust: none;
            background-color: #ffffff;
            color: #718096;
            height: 100%;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        p,
        ul,
        ol,
        blockquote {
            line-height: 1.4;
            text-align: left;
        }

        a {
            color: #3869d4;
        }

        a img {
            border: none;
        }

        /* Typography */

        h1 {
            color: #3d4852;
            font-size: 18px;
            font-weight: bold;
            margin-top: 0;
            text-align: left;
        }

        h2 {
            font-size: 16px;
            font-weight: bold;
            margin-top: 0;
            text-align: left;
        }

        h3 {
            font-size: 14px;
            font-weight: bold;
            margin-top: 0;
            text-align: left;
        }

        p {
            font-size: 15px;
            line-height: 1.2em;
            margin-top: 0;
            text-align: left;
        }

        p.sub {
            font-size: 12px;
        }

        img {
            max-width: 100%;
        }

        /* Layout */

        .wrapper {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
            background-color: #edf2f7;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .content {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        /* Header */

        .header {
            padding: 25px 0;
            text-align: center;
        }

        .header a {
            color: #3d4852;
            font-size: 19px;
            font-weight: bold;
            text-decoration: none;
        }

        /* Logo */

        .logo {
            height: 80px;
            max-height: 80px;
            width: 80px;
        }



        .footer-banner {
            max-height: 360px;
            width: 360px;
        }


        /* Body */

        .body {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
            background-color: #edf2f7;
            border-bottom: 1px solid #edf2f7;
            border-top: 1px solid #edf2f7;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .inner-body {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 570px;
            background-color: #ffffff;
            border-color: #e8e5ef;
            border-radius: 2px;
            border-width: 1px;
            box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015);
            margin: 0 auto;
            padding: 0;
            width: 570px;
        }

        /* Subcopy */

        .subcopy {
            border-top: 1px solid #e8e5ef;
            margin-top: 25px;
            padding-top: 25px;
        }

        .subcopy p {
            font-size: 13px;
        }

        /* Footer */

        .footer {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 570px;
            margin: 0 auto;
            padding: 0px 0px 30px 0px;
            text-align: center;
            width: 570px;
        }

        .footer p {
            color: #b0adc5;
            font-size: 12px;
            text-align: center;
        }

        .footer a {
            color: #b0adc5;
            text-decoration: underline;
        }

        /* Tables */

        .table table {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
            margin: 15px 0px 30px 0px;
            width: 100%;
        }

        .table th {
            border-bottom: 1px solid #edeff2;
            margin: 0;
            padding-bottom: 8px;
        }

        .table td {
            color: #555556;
            font-size: 13px;
            line-height: 15px;
            margin: 0;
            padding: 10px 0;
        }

        .content-cell {
            max-width: 100vw;
            padding: 32px 32px 0px 32px;
            border-radius: 3px;
            border-top: solid 8px #d3a188;
        }

        /* Buttons */

        .action {
            -premailer-cellpadding: 0;
            -premailer-cellspacing: 0;
            -premailer-width: 100%;
            margin: 30px auto;
            padding: 0;
            text-align: center;
            width: 100%;
        }

        .button {
            -webkit-text-size-adjust: none;
            border-radius: 4px;
            color: #fff;
            display: inline-block;
            overflow: hidden;
            text-decoration: none;
        }

        .button-blue,
        .button-primary {
            background-color: #2d3748;
            border-bottom: 8px solid #d3a188;
            border-left: 18px solid #d3a188;
            border-right: 18px solid #2d3748;
            border-top: 8px solid #2d3748;
        }

        .button-green,
        .button-success {
            background-color: #48bb78;
            border-bottom: 8px solid #48bb78;
            border-left: 18px solid #48bb78;
            border-right: 18px solid #48bb78;
            border-top: 8px solid #48bb78;
        }

        .button-red,
        .button-error {
            background-color: #e53e3e;
            border-bottom: 8px solid #e53e3e;
            border-left: 18px solid #e53e3e;
            border-right: 18px solid #e53e3e;
            border-top: 8px solid #e53e3e;
        }

        /* Panels */

        .panel {
            border-left: #2d3748 solid 4px;
            margin: 21px 0;
        }

        .panel-content {
            background-color: #edf2f7;
            color: #718096;
            padding: 16px;
        }

        .panel-content p {
            color: #718096;
        }

        .panel-item {
            padding: 0;
        }

        .panel-item p:last-of-type {
            margin-bottom: 0;
            padding-bottom: 0;
        }

        /* Utilities */

        .break-all {
            word-break: break-all;
        }

        .text-start{
            text-align: start;
        }
        .text-end{
            text-align: end;
            border: 1px solid #e5e5e5;
            padding: 5px 8px;
            max-width: 190px;
            margin-left: auto;
        }

        .client-box-start{
                text-align: start;
                border: 1px solid #e5e5e5;
                padding: 5px 8px;
                max-width: 300px;
                margin-right: auto;
                margin-bottom: 20px;
        }

     

        table.invoice-table th, table.invoice-table td{
            border: 1px solid !important;
            padding: 2px !important;
        }
        table.invoice-table{
            border-collapse: collapse !important;
        }
        .h1-bt{
            margin-bottom: -12px !important;
            line-height: 15px !important;
        }

           .text-green{
            color: #079007 !important;
        }
        .payable_amount{
            font-size: 15px !important;
            font-weight: bold;
            border-top: solid 2px #555556 !important;
            padding: 5px 0px 5px 2px !important;
        }

    </style>
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center">
            <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <?php echo $__env->make("emails.layouts.header", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <!-- Email Body -->
                <tr>
                    <td class="body" width="100%" cellpadding="0" cellspacing="0">
                        <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0"
                               role="presentation">
                            <!-- Body content -->
                            <tr>
                                <td class="content-cell">
                                    <?php echo e(Illuminate\Mail\Markdown::parse($slot)); ?>


                                    <?php echo e($subcopy ?? ''); ?>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php echo $__env->make("emails.layouts.footer", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/vendor/mail/html/layout.blade.php ENDPATH**/ ?>