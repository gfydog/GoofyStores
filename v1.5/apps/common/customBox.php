<!-- Custom Alert Dialog -->
<div id="custom-alert-box" class="custom-box">
    <div class="custom-box-content">
        <p id="custom-alert-box-message" class="custom-box-message"></p>
        <button id="alert-ok" class="btn btn-primary">OK</button>
    </div>
</div>

<!-- Custom Confirmation Dialog -->
<div id="custom-confirm-box" class="custom-box">
    <div class="custom-box-content">
        <p id="custom-confirm-box-message" class="custom-box-message"></p>
        <button id="confirm-no" class="btn btn-secondary">Cancelar</button>
        <button id="confirm-yes" class="btn btn-primary">Aceptar</button>
    </div>
</div>

<script>
    // Function to show a custom alert dialog
    function showAlert(message, callback = () => {}) {
        $("#custom-alert-box-message").html(message);
        $("#custom-alert-box").fadeIn();
        $("body").addClass("blur");

        // Event handler for OK button click
        $("#alert-ok").click(function() {
            $("#custom-alert-box").fadeOut();
            $("body").removeClass("blur");
            callback();
        });
    }

    // Function to show a custom confirmation dialog
    function showConfirm(message, callback, cancel = () => {}) {
        $("#custom-confirm-box-message").html(message);
        $("#custom-confirm-box").fadeIn();
        $("body").addClass("blur");

        // Event handler for confirmation button click
        $("#confirm-yes").click(function() {
            $("#custom-confirm-box").fadeOut();
            $("body").removeClass("blur");
            callback();
        });

        // Event handler for cancel button click
        $("#confirm-no").click(function() {
            $("#custom-confirm-box").fadeOut();
            $("body").removeClass("blur");
            cancel();
        });
    }
</script>

<style>
    /* Styles for the custom alert and confirmation dialogs */
    .custom-box {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
    }

    /* Styles for the content within the custom dialog */
    .custom-box-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 300px;
        background-color: #ffffff;
        border-radius: 5px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Styles for the message in the custom dialog */
    .custom-box-message {
        margin-bottom: 20px;
        color: #000;
    }

    /* Styles for buttons within the custom dialog */
    .btn {
        padding: 10px;
        border: none;
        border-radius: 3px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    /* Styles for the primary button in the custom dialog */
    .btn-primary {
        background-color: #007bff;
        color: #fff;
    }

    /* Hover effect for the primary button */
    .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Styles for the secondary button in the custom dialog */
    .btn-secondary {
        background-color: #fff;
        color: #000;
        border: 1px solid #ccc;
    }

    /* Effects to blur elements outside of the custom dialog */
    .blur #custom-box {
        filter: none;
    }

    .blur > *:not(.custom-box) {
        filter: blur(5px);
    }
</style>
