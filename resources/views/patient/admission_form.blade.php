<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admission Form</title>
<style>

body{
    background:#ddd;
    display:flex;
    justify-content:center;
    padding:10px;
    font-family:'Times New Roman', Times, serif;
}

.a4{
    width:210mm;
    min-height:297mm;
    background:#fff;
    padding:10mm;
    border:1px solid #000;
    box-sizing:border-box;
    margin-bottom:15px;
}

@media print{
    body{background:none;padding:0;margin:0}
    .a4{border:none;margin:0;padding:10mm;}
}

.topText{
    display:flex;
    justify-content:space-between;
    font-size:13px;
    font-weight:bold;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:6px;
}

.header img{
    width:70px;
    height:70px;
}

.title{
    text-align:center;
    line-height:1.3;
}

.title h1{
    font-size:24px;
    margin:0;
    font-weight:bold;
}

.title p{
    font-size:13.5px;
    margin:2px 0;
}

.divider{
    border-top:2px solid #000;
    margin:8px 0;
}

.contentText{
    text-align:center;
    font-size:14px;
    margin:6px 0 12px 0;
    font-weight:bold;
}

.form{
    display:flex;
    justify-content:space-between;
}

.leftForm{
    width:62%;
    font-size:14.5px;
}

.leftForm .field{
    margin:6px 0;
    display:flex;
    align-items:center;
}

.leftForm .field span{
    width:6%;
}

.leftForm .field label{
    width:44%;
}

.leftForm .field input{
    width:50%;
    border:1px solid #000;
    padding:3px;
    font-size:14px;
    outline:none;
}

.photoBox{
    width:33%;
    border:2px solid #000;
    height:125px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:15px;
    font-weight:bold;
}

.sectionTitle{
    font-size:15px;
    font-weight:bold;
    border-bottom:1.4px solid #000;
    margin:12px 0 6px 0;
    padding-bottom:3px;
}

.paragraph{
    font-size:14px;
    text-align:justify;
    line-height:1.4;
    margin-bottom:6px;
}

.fieldRow{
    display:flex;
    margin:5px 0;
    font-size:14px;
    align-items:center;
}

.fieldRow span{
    width:6%;
}

.fieldRow label{
    width:60%;
}

.fieldRow input{
    width:34%;
    border:1px solid #000;
    padding:3px;
    font-size:14px;
    outline:none;
}

.footerText{
    text-align:right;
    font-size:14px;
    margin-top:15px;
    font-weight:bold;
}

.bottomLine{
    border-top:2px solid #000;
    margin-top:30px;
}

.bottomFooter{
    display:flex;
    justify-content:space-between;
    font-size:14px;
    font-weight:bold;
    margin-top:5px;
}

.footerSection{
    font-size:14px;
    text-align:center;
    font-weight:bold;
    margin-top:18px;
}

.signatureRow{
    display:flex;
    justify-content:space-between;
    margin-top:35px;
    font-size:14px;
    font-weight:bold;
}

.signatureRow div{
    width:42%;
    text-align:center;
    border-top:2px solid #000;
    padding-top:5px;
}

</style>
</head>
<body>

<form>

<!-- =================== PAGE 1 =================== -->
<div class="a4">

    <div class="topText">
        <div>ISO 9001:2015<br>REG.NO:132</div>
        <div>73396 66737, 93444 55737</div>
    </div>

    <div class="header">
        <img src="assets/img/logo.webp" alt="logo">
        <div class="title">
            <h1>AMENDS CHARITABLE TRUST</h1>
            <p>An ISO 9001:2015 Certified Foundation</p>
            <p>Psychiatric & De-addiction Treatment Centre</p>
            <p>No.9/3, Lakshmi Mahal, Annampaarpatti,<br>
            Vathalakundu Road, Usilampatti – 625 532. Madurai District.</p>
            <p>E-mail : amendscharitabletrust@gmail.com</p>
        </div>
        <img src="assets/img/logo.webp" alt="logo">
    </div>

    <div class="divider"></div>

    <div class="contentText">
        I voluntarily wish to admit myself for treatment. I understand and agree to follow the rules and regulations of the centre.
    </div>

    <div class="form">
        <div class="leftForm">
            <div class="field"><span>1.</span><label>Name:</label><input type="text" required></div>
            <div class="field"><span>2.</span><label>Age, Date of Birth:</label><input type="text" required></div>
            <div class="field"><span>3.</span><label>ID Proof:</label><input type="text"></div>
            <div class="field"><span>4.</span><label>Address:</label><input type="text"></div>
            <div class="field"><span>5.</span><label>Father/Guardian Name:</label><input type="text"></div>
            <div class="field"><span>6.</span><label>Phone Number:</label><input type="text" required></div>
            <div class="field"><span>7.</span><label>Religion:</label><input type="text"></div>
            <div class="field"><span>8.</span><label>Education:</label><input type="text"></div>
            <div class="field"><span>9.</span><label>Marital Status:</label><input type="text"></div>
            <div class="field"><span>10.</span><label>Occupation:</label><input type="text"></div>
        </div>

        <div class="photoBox">PHOTO</div>
    </div>

    <div class="footerText">Place :</div>

    <div class="bottomLine"></div>

    <div class="bottomFooter">
        <div>Signature of the Applicant</div>
        <div>Authorized Signature</div>
    </div>

</div>

<!-- =================== PAGE 2 =================== -->
<div class="a4">

    <div class="pageNo">2</div>

    <div class="sectionTitle">Medical Information:</div>

    <div class="paragraph">1. Have you previously undergone treatment for psychiatric or de-addiction issues? If yes, provide details.</div>
    <div class="fieldRow"><span>1.</span><label></label><input type="text"></div>

    <div class="paragraph">2. Are you currently on any medication? If yes, list them below.</div>
    <div class="fieldRow"><span>2.</span><label></label><input type="text"></div>

    <div class="paragraph">3. Do you have any chronic medical conditions? If yes, mention details.</div>
    <div class="fieldRow"><span>3.</span><label></label>
