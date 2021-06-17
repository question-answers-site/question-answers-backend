<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QA</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
</head>
<style>
    .error-input {
        border: red 1px solid;
        box-shadow: 0 0 3px red;
    }

    .app-card {
        border-radius: 5px;
        padding: 50px;
        box-shadow: 4px 4px 6px grey;
    }

    .success-input {
        border: green 1px solid;
        box-shadow: 0 0 3px green;
    }

    .error-form {
        box-shadow: 6px 6px 6px grey, 0 0 6px red;
    }

    .success-form {
        box-shadow: 6px 6px 6px grey, 0 0 6px green;
    }

    input {
        margin-bottom: 10px;
    }

    .label-control {
        color: #3493ff;
    }

    .form-group {
        margin-bottom: 30px;
    }

    .error-message {
        color: red;
    }

    .app-btn-outline-info {
        font-size: larger;
        color: #288eff;
        background-color: white;
        width: max-content;
        padding: 5px 15px;
        border: #288eff 1px solid;
        border-radius: 4px;
        outline: none;
        margin-top: 20px;
    }

    .app-btn-outline-info:hover {
        background-color: #3493ff;
        color: white;
    }

</style>
<body>

<h1 style="margin:0 0 100px 0;padding: 25px 0" class="text-center bg-primary">QA Site</h1>

<div style="width: 500px ; margin:40px auto;" class="app-card">

    @if(session()->has('error_message'))
        <div class="text-center"
            style="background-color: rgba(255,0,0,0.68);color: white;padding: 10px;margin-bottom:50px;border-radius: 10px">
            {{session('error_message')}}
        </div>
    @endif

    <form method="post" action="{{route('reset_password')}}">
        {{csrf_field()}}
        <input type="hidden" name="password_token" value="{{$token}}">
        <div class="form-group">
            <label class="label-control" for="email">E-mail :</label>
            <input id="email" type="email" rules="required,email"
                   class="form-control"
                   name="email" placeholder="Your Email" value="{{old('email','')}}">
        </div>

        <div class="form-group">
            <label class="label-control" for="password">Password :</label>
            <input id="password" placeholder="New Password" type="password" rules="required,minLength"
                   class="form-control" name="password"
            >
        </div>
        <div class="form-group">
            <label class="label-control" for="confirmed-password">Confirmed Password :</label>
            <input id="confirmed-password" type="password" rules="required,confirmed"
                   class="form-control" name="password_confirmation"
                   disabled="true" placeholder="Confirmation Password"
            >
        </div>
        <div class="text-right">
            <button type="submit" id="submit-btn" class="app-btn-outline-info">
                Save
            </button>
        </div>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" type="text/javascript"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>


<script>
    let formCorrect = true
    let $form = $('form')
    let $passwordField = $('#password')
    let $confirmedPasswordField = $('#confirmed-password')
    $form.on('submit', submitForm)
    $form.on('keyup', checkRules)

    function clearErrors($target) {
        $target.removeClass(['error-input', 'success-input'])
        $target.next('span.error-message').remove()
    }

    function toFunctions(data) {
        data = data.split(',')
        return data.map(item => {
            if (item === 'confirmed') return confirmed
            if (item === 'required') return required
            if (item === 'email') return emailCheck
            if (item === 'minLength') return minLength
            return null
        }).filter(item => item)
    }

    function execRules(rules, $target) {
        res = true
        for (let key in rules) {
            item = rules[key]

            let tmp = true
            if (item.name === 'confirmed')
                tmp = confirmed($passwordField, $confirmedPasswordField)
            if (item.name === 'required')
                tmp = required($target)
            if (item.name === 'emailCheck')
                tmp = emailCheck($target)
            if (item.name === 'minLength')
                tmp = minLength($target, 4)
            if (typeof tmp === "string") {
                res = tmp
                break
            }
        }
        return res
    }

    function checkFieldRules($target) {
        clearErrors($target)
        let rulesAttr = $target.attr('rules')
        if (rulesAttr === undefined) return
        let rules = toFunctions(rulesAttr)
        let res = execRules(rules, $target)

        if (typeof res === "string") {
            $target.addClass('error-input')
            $target.after(`<span class="error-message">${res}</span>`)
            return false
        }
        $target.addClass('success-input')
        return true
    }

    function checkRules(event) {

        let isCorrect = checkFieldRules($(event.target))

        if (event.target.id === 'password' && !isCorrect) {
            $confirmedPasswordField.attr('disabled', true)
        } else if (event.target.id === 'password') {
            $confirmedPasswordField.attr('disabled', false)
            checkFieldRules($confirmedPasswordField)
        }
    }

    function checkAllField(event) {
        formCorrect = true
        $('form input').each((key, field) => {
            if (field.type !== 'hidden')
                formCorrect &= checkFieldRules($(field))
        })
        if (formCorrect)
            $('.app-card').addClass('success-form')
        else
            $('.app-card').addClass('error-form')
    }

    function submitForm(event) {
        checkAllField(event)
        if (!formCorrect) {
            event.preventDefault()
        }
    }

    function confirmed($fieldA, $fieldB) {
        return ($fieldA.val() === $fieldB.val()) || 'The Password IS Not Confirmed Correctly'
    }

    function required($field) {
        return !!$field.val() || 'This Field Is Required'
    }

    function emailCheck($field) {
        return true
    }

    function minLength($field, size) {
        return $field.val().length >= size || `This Field most Contain At Least ${size} Character`
    }

</script>
</body>
</html>
