<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>List of Available Contracts</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous" async="">

    <!-- jQuery UI Sunny theme -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/sunny/jquery-ui.min.css" integrity="sha512-t/yl85emxwarY4DzF8RUddWA+01SUMtURTPNve/zvFnzmor8mM2TMu2tWff/SdeXOEyrmenasu2R2/UEeDE+pw==" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/sunny/theme.min.css" integrity="sha512-D7I8i+5c8pBasr1IqvyTFr6wQFHKXJ9XWlij0Y3W9zBjofUcXY24dLaGJI8zLe252GhHuH6L6PvWKXGGrkA4DQ==" crossorigin="anonymous" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <!-- jQuery UI DatePicker.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js" integrity="sha512-RCgrAvvoLpP7KVgTkTctrUdv7C6t7Un3p1iaoPr1++3pybCyCsCZZN7QEHMZTcJTmcJ7jzexTO+eFpHk4OCFAg==" crossorigin="anonymous"></script>
    <!-- jQuery UI [Has to be below DatePicker, for some reason... -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" />
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

input.datepicker {
    width: 8em;
    text-align: center;
}

</style>
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

$( function() {
    // var elem = document.createElement('input');
    // elem.setAttribute('type', 'date');
    //
    // if ( elem.type === 'text' ) {
        $('input.datepicker').datepicker();

        // @FIXME: Need to enforce MM/DD/YYYY With automatic handling of the "/", including copying and pasting.
    //Put our input DOM element into a jQuery Object
    // var $jqDate = jQuery('input.datepicker');
    //
// //Bind keyup/keydown to the input
//     $jqDate.bind('keyup','keydown', function(e){
//
//         //To accomdate for backspacing, we detect which key was pressed - if backspace, do nothing:
//         if(e.which !== 8) {
//             var numChars = $jqDate.val().length;
//             if(numChars === 2 || numChars === 5){
//                 var thisVal = $jqDate.val();
//                 thisVal += '/';
//                 $jqDate.val(thisVal);
//             }
//         }
//
//
//         date.addEventListener('input', function(e) {
//             this.type = 'text';
//             var input = this.value;
//             if (/\D\/$/.test(input)) input = input.substr(0, input.length - 3);
//             var values = input.split('/').map(function(v) {
//                 return v.replace(/\D/g, '')
//             });
//             if (values[0]) values[0] = checkValue(values[0], 12);
//             if (values[1]) values[1] = checkValue(values[1], 31);
//             var output = values.map(function(v, i) {
//                 return v.length == 2 && i < 2 ? v + ' / ' : v;
//             });
//             this.value = output.join('').substr(0, 14);
//         });
//     });
    // }
} );
</script>
</head>
<body>
    <h1>List of Available Contracts</h1>
</body>
</html>