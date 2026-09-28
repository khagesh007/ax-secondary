<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fillable PDF</title>
    <style>

        input[type="text"] {
            width: 300px;
            padding: 5px;
            font-size: 14px;
        }
        table{
            border-collapse: collapse; border: none;
        }
        td{
            border: none;
            text-align: left;
    padding-left: 0.35em;
    padding-right: 0.35em;
    padding-top: 0.35em;
    padding-bottom: 0.35em;
    vertical-align: top;
        }
/* Hide the default radio button */
 input[type="radio"] {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Create a custom square checkmark */
input[type="radio"] .checkmark {
  position: relative;
  display: inline-block;
  width: 20px;  /* Set the width of the square */
  height: 20px; /* Set the height of the square */
  background-color: #fff;
  border: 2px solid #ccc; /* Border for the square */
  border-radius: 4px; /* Small border radius to make edges slightly rounded */
  margin-right: 10px; /* Space between the square and text */
}

/* Style for checked state */
 input[type="radio"]:checked + .checkmark {
  background-color: #007bff; /* Change background color when checked */
  border-color: #007bff; /* Change border color when checked */
}

/* Create the inner dot (for checked state) */
 input[type="radio"]:checked + .checkmark:after {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  width: 10px;  /* Size of the inner dot */
  height: 10px; /* Size of the inner dot */
  background-color: white; /* Dot color */
  transform: translate(-50%, -50%);
  border-radius: 2px; /* Make the dot square-shaped */
}

/* Hover effect */
 .checkmark:hover {
  background-color: #f1f1f1; /* Lighten background color on hover */
}
input[type="text"] {
  background-color: #dd9f64; /* Change background color when checked */
 border: 0px;
}


    </style>
</head>
<body>
    <div
    {{-- style="background-image: url('{{ 'pdf-bg-image.jpg' }}'); background-size: cover;
     background-position: center;
     width: 100%; height: 100%; position:
     absolute; top: 0; left: 0; z-index: -1;" --}}
    >
    <table style="width: 100%;">
        <tr>
            <!-- Left-aligned image -->
            <td style="width: 30%; text-align: left;">
                <img src="{{ public_path('images/logo140.png') }}" style="width: 100px;">
            </td>

            <!-- Right-aligned text -->
            <td style="width: 70%; text-align: right; margin-top:0px;">
                <span style="font-size: 16px; font-weight: bold;">REAL ESTATE MARKETING AGREEMENT</span>
                <br>
                <span style="font-size: 16px;">(OWNER/LANDLORD)</span>
            </td>
        </tr>
    </table>
    <hr>
    <table style="width: 100%;">
        <tr>
            <!-- Left-aligned image -->
            <td style="width: 30%; text-align: left;">
             Sale <input type="radio" class="custom-radio" name="num_languages" value="1" checked="checked" > &nbsp;&nbsp;&nbsp;Rent <input type="radio" class="custom-radio" name="num_languages" value="2"  >
            </td>

            <!-- Right-aligned text -->
            <td style="width: 70%; text-align: right; margin-top:0px;">
                <span style="font-size: 14px;font-weight: bold; background:#dd9f64; color:white; padding:4px 4px 4px 4px;">FORM A</span>
            </td>
        </tr>
    </table>

    <table style="width: 100%;">
        <tr style="background:#dd9f64;">
            <!-- Left-aligned image -->
            <td style="width: 50%; text-align: left; color:white;">
           Part 1
            </td>

            <!-- Right-aligned text -->
            <td style="width: 50%; text-align: left; margin-top:0px;">
                <span style="font-size: 14px; color:white;">PARTIES</span>
            </td>
        </tr>
        <tr style="background:rgb(206, 206, 206); padding 5pt; ">
            <!-- Left-aligned image -->
            <td style="width: 50%; font-size: 12px; text-align: left; color:rgb(0, 0, 0);
            padding 4px 4px 4px 4px;
            ">
                A. THE AGENT/ BROKER
            </td>

            <!-- Right-aligned text -->
            <td style="width: 50%; font-size: 12px; text-align: left; margin-top:0px;">
                <span style=" color:rgb(0, 0, 0);">B. THE PROPERTY OWNER/LANDLORD
                    <span style="font-size: 12px;">(As per Title Deed)</span></span>
            </td>
        </tr>
    </table>

    <table style="margin-top:5px;">
        <tr >
            <td style="width: 50%; text-align: left; font-size:11px">
                    Name of the Establishment: <b>Property Matters Software Design LLC.</b>
            </td>
            <td style="width: 50%; text-align: left;">
                <label for="name">Owner/Landlord:</label>
                <input type="text" name="name" id="name" value="khagesh">
            </td>
        </tr>
        <tr >
            <td style="width: 50%; text-align: left; font-size:11px">
                Office Contact Details: Po Box: 500030, Ofce 204, Bldg 13,
                Bay Square, Business Bay, Dubai, UAE
            </td>
            <td style="width: 50%; text-align: left;">
                <label for="name">Owner/Landlord:</label>
                <input type="text" name="name" id="name" value="khagesh">
            </td>
        </tr>
        <tr >
            <td style="width: 50%; text-align: left; font-size:11px">
               Tel: +971 5077 10936
            </td>
            <td style="width: 50%; text-align: left;">
                <label for="name">Owner Email:</label>
                <input type="text" name="name" id="name" value="test@gmail.com">
            </td>
        </tr>
        <tr >
            <td style="width: 50%; text-align: left; font-size:11px">
              Email: info@propcrm.com
            </td>
            <td style="width: 50%; text-align: left;">
                <b>Basis of Appointment:</b>
            </td>
        </tr>
        <tr >
            <td style="width: 50%; text-align: left; font-size:11px">
                ORN: Rera No.: 12345 DED Lisc: 12345
            </td>
            <td style="width: 50%; text-align: left;">

            </td>
        </tr>
        <tr >
            <td style="width: 50%; text-align: left; font-size:11px">
              <b>Name of the Registered Agent</b>
            </td>
            <td style="width: 50%; text-align: left;font-size:10px;">
                Exclusive <input type="radio" class="custom-radio" name="num_languages" value="1" checked="checked" > &nbsp;&nbsp;&nbsp;Sole Agent <input type="radio" class="custom-radio" name="num_languages" value="2"  > Several Agents &nbsp;&nbsp;&nbsp;Sole Agent <input type="radio" class="custom-radio" name="num_languages" value="3"  > &nbsp;(Maximum 3)
            </td>
        </tr>
    </table>
<table>
    <tbody>
    <tr >
        <td style="width: 50%; text-align: left; font-size:11px">
        Name   <input type="text" name="name" id="name">
        <br>
        BRN <input type="text" name="brn" id="brn">   Date of Issued: <input type="text" name="doi" id="doi">
        <br>
        Mobile <input type="text" name="mobile" id="mobile">
        <br>
        Email <input type="text" name="email" id="email">

        <br>
        <p style="color:gray">
        <b>DECLARATION BY AGENT</b>
        <br>
    <span style="font-style: italic;">I hereby declare, to be in compliance with UAE Laws and Regulations and to the Real
    Estate Brokers Code of Ethics. I have provided the Owner/Landlord with the market price
    range for the property according to DLD indexes</span>
</p>
        </td>
        <td style="width: 50%; text-align: left;font-size:10px;">
            Start Date of This Appointment: <input type="text" name="mobile" id="mobile">
            <br>
            End Date of This Appointment: <input type="text" name="email" id="email">

            <br>
          <p style="color:gray">
            <b>DECLARATION BY OWNER/LANDLORD</b>
            <br>
            <span style="font-style: italic;">
I/ We hereby declare that I am/We are the Owner(s) of the property. I/We undertake in
this agreement with the Agent, to sell/ lease the said property when an ofer to purchase/
lease is presented by the Agent and accepted by myself. I/We acknowledge and confirm
the agreed commission/brokerage fee to be paid to the Agent according to Part 3 of this
agreement described hereinbelow</span>
          </p>
        </td>
    </tr>
</tbody>
</table>

<table style="width: 100%;">
    <tbody>
    <tr style="background:#dd9f64;">
        <!-- Left-aligned image -->
        <td style="width: 35%; text-align: left; color:white;">
       Part 2.
        </td>

        <!-- Right-aligned text -->
        <td style="width: 65%; text-align: left; margin-top:0px;">
            <span style="font-size: 14px; color:white;">THE PROPERTY DETAILS</span>
        </td>
    </tr>
</tbody>
</table>
<div>
    <input type="radio" name="property_type" value="Villa"> Villa &nbsp; &nbsp;

    <input type="radio" name="property_type" value="Apartment"> Apartment &nbsp; &nbsp;

    <input type="radio" name="property_type" value="Whole_Building"> Whole Building &nbsp;&nbsp;

    <input type="radio" name="property_type" value="Plot_Land"> Plot/Land &nbsp;&nbsp;

    <input type="radio" name="property_type" value="Warehouse"> Warehouse &nbsp;&nbsp;
    <input type="radio" name="property_type" value="Office"> Office &nbsp;&nbsp;
    <input type="radio" name="property_type" value="Retail"> Retail &nbsp;&nbsp;
</div>
<br>
<b>Present Use:</b>
<br>
<input type="radio" name="use" value="Warehouse"> Residential &nbsp;&nbsp;
<input type="radio" name="use" value="Office"> Commercial &nbsp;&nbsp;
<input type="radio" name="use" value="Retail"> Under Construction &nbsp;&nbsp;

<table>
    <tbody>
<tr >
    <td style="width: 50%; text-align: left;">
        Building Name:   <input type="text" name="name" id="name">
    <br>
    </td>
    <td>
        Community:
        <input type="text" name="community" id="community">
    </td>
</tr>
<tr >
    <td style="width: 50%; text-align: left;">
        Unit/ Villa No:   <input type="text" name="name" id="name">
    <br>
    </td>
    <td>
        Floor/street:
        <input type="text" name="floor" id="community">
        Plot No:
        <input type="text" name="plot_no" id="community">
    </td>
</tr>
<tr>
    <td>
        Build-up-area:
        <input type="text" name="boua" id="community">
    </td> <td>
        Plot Area:
        <input type="text" name="palot_area" id="community">
    </td>
</tr>
    <tr>
    <td>
        No. of beds:
        <input type="text" name="no_of_beds" id="community">
    </td><td>
        Number of bathrooms:
        <input type="text" name="no_of_bath" id="community">
    </td>
</tr>
</tbody>
</table>


</div>
    <script type="text/javascript">
        // document.getElementById('fillableForm').addEventListener('submit', function(event) {
        //     event.preventDefault();
        // });
    </script>
</body>
</html>
