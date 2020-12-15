<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$contractTitle}}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous" async="">
    <style>
header div#updateSuccessful {
    position: fixed;
    top: 0;
    right: 0;
}
section.contract {
    padding: 20px 30px;
    border: 1px black solid;
    background: #F8F8F8;
    max-width: 42.5em;
    font-family: serif !important;;
    text-align: justify !important;
    white-space: pre-wrap;
}
section.contract button {
    font-family: sans-serif;
}

.popup-tag{
    position: absolute;
    display:none;
    background-color: #785448CC;
    color: white;
    padding: 10px;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    -webkit-filter: drop-shadow(0 1px 10px rgba(113,158,206,0.8));
}

.popup-tag ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

    </style>
    <script
            src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
            crossorigin="anonymous"></script>
<script>
// From: https://stackoverflow.com/a/48422455/430062
function getSelected()
{
    if (window.getSelection) {
        console.log(window.getSelection());
        return window.getSelection();
    }
    else if (document.getSelection)
    {
        console.log(document.getSelection());
        return document.getSelection();
    }
    else {
        var selection = document.selection && document.selection.createRange();
        if (selection.text) { return selection.text; }
        return false;
    }

    return false;
}

function recordSelectedText()
{
    var sel, range;
    if (window.getSelection) {
        sel = window.getSelection();
        if (sel.rangeCount) {
            recordSelectedText.range = sel.getRangeAt(0);
        }
    } else if (document.selection && document.selection.createRange) {
        recordSelectedText.range = document.selection.createRange();
        recordSelectedText.range.text = replacementText;
    }
}

function replaceSelectedText(replacementText) {
    if (!recordSelectedText.range) {
        return;
    }

    recordSelectedText.range.deleteContents();
    recordSelectedText.range.insertNode(document.createTextNode(replacementText));
}

$(document).ready(function() {
    // $('.contract').text($('.contract').text().replace('\_', '_'))
    $('.contract').text($('.contract').text().replaceAll('\\_', '_'));

    $('.replaceText').click(function () {
        replaceSelectedText('[[' + $(this).data('text') + ']]');
        $("div.popup-tag").css('display', 'none');
    });

    $('.contract').mouseup(function(event) {
        const $popupTag = $("div.popup-tag");
        const selection = $.trim(getSelected());
        const popupHeight = $popupTag.height();

        if (selection !== '') {
            $popupTag.css("display", "block");
            $popupTag.css("top",  event.pageY - (popupHeight / 2));
            $popupTag.css("left", event.pageX + 50);
        } else {
            $popupTag.css("display","none");
        }
        recordSelectedText();
    });

    $('button#editContract').click(function () {
        const contractData = {
            name: $('input#contract_name').val(),
            description: $('input#contract_description').val(),
            contract: $('section#contract').text()
        };

        $('#updateSuccessful').addClass('d-none');
        $.ajax({
            url: '/contracts-tracker/api/contract/' + $('input#contractId').val(),
            type: 'PUT',
            contentType: 'application/json',
            processData: false,
            dataType: 'json',
            data: JSON.stringify(contractData),
        })
            .then(function (data) {
                $('#updateSuccessful').removeClass('d-none');
            })
            .catch(function (error) {
                alert(data);
            });
    });
});
</script>
</head>
<body>
@if (!$foundContract)
    <div class="alert alert-danger">
        <h1>Missing Contract</h1>

        The contract with the ID <strong>"{{$contractId}}"</strong> could not be found.
    </div>
@else
    <header>
        <h1>Edit the Contract</h1>
        <p>Please edit this contract to your specifications.</p>
        <p>While the entire document is editable, we have added a popup dialogue for ease-of-use.</p>
        <p>Just select the fields you want to replace and click the desired button.</p>
        <div class="d-none alert alert-success" id="updateSuccessful">The Contract was successfully updated.</div>
    </header>
    <section class="contract_meta">
        <form>
            <input type="hidden" id="contractId" value="{{$contractId}}"/>
            <div class="form-group">
                <label for="contract_name"><strong>Contract Name</strong></label>
                <input type="text" class="form-control col-md-6" id="contract_name" aria-describedby="emailHelp" placeholder="Contract's name"
                       value="Short NDA">
                <small id="emailHelp" class="form-text text-muted">This is how we will refer to the contract everywhere else.</small>
            </div>
            <div class="form-group">
                <label for="contract_description"><strong>Description</strong></label>
                <input type="text" class="form-control col-md-6" id="contract_description" placeholder="Briefly describe the contract's purpose."
                       value="A non-wordy basic Non-Disclosure Agreement">
            </div>
        </form>
    </section>
    <section class="contract" id="contract" contenteditable="true">
{!! $contractText !!}
    </section>
    <button class="btn btn-primary" id="editContract">Save Contract</button>
    <div class="popup-tag" style="font-family: sans-serif; font-size: 90%">
        <ul>
            <li class="replaceText" data-text="DATE PICKER"><i class="fas fa-calendar-check" title="date picker"></i> Date Picker</li>
            <li class="replaceText" data-text="APPLICANT NAME"><i class="fas fa-underline"></i> Applicant Name</li>
            <li class="replaceText" data-text="APPLICANT SIGNATURE"><i class="fas fa-file-signature" title="signature"></i> Applicant Signature</li>
            <li class="replaceText" data-text="CORPORATE NAME"><i class="fas fa-underline"></i> Corporate Name</li>
            <li class="replaceText" data-text="CORPORATE REP"><i class="fas fa-underline"></i> Corporate Rep</li>
            <li class="replaceText" data-text="CORPORATE TITLE"><i class="fas fa-underline"></i> Corporate Title</li>
            <li class="replaceText" data-text="CORPORATE SIGNATURE"><i class="fas fa-file-signature" title="signature"></i> Corporate Signature</li>
            <li class="replaceText" data-text="CITY"><i class="fas fa-city" title="signature"></i> City</li>
            <li class="replaceText" data-text="STATE"><i class="fas fa-flag" title="signature"></i> State</li>
            <li class="replaceText" data-text="EMAIL"><i class="fas fa-envelope"></i> Email Address</li>
            <li class="replaceText" data-text="PHONE"><i class="fas fa-phone"></i> Phone Number</li>
        </ul>
    </div>
@endif
</body>
</html>
