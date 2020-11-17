function generateConfig () {
	let db_host = document.getElementById("db-host").value;
	let db_user = document.getElementById("db-user").value;
	let db_pswd = document.getElementById("db-pswd").value;
	let db_name = document.getElementById("db-name").value;
	let ami_host = document.getElementById("ami-host").value;
	let ami_user = document.getElementById("ami-user").value;
	let ami_pass = document.getElementById("ami-pass").value;
	let ami_port = document.getElementById("ami-port").value;
	let ami_timeout = document.getElementById("ami-timeout").value;
	let yandex_tts = document.getElementById("yandex").value;
	let us1_link = document.getElementById("us1-link").value;
	let us1_api = document.getElementById("us1-api").value;
	let us2_link = document.getElementById("us2-link").value;
	let us2_api = document.getElementById("us2-api").value;

	var radios = document.getElementsByName('radio');

    for (var i = 0, length = radios.length; i < length; i++) {
        if (radios[i].checked) {
            if (radios[i].value == "create-db-no") {
                var create_db = false;
            }
            if (radios[i].value == "create-db-yes") {
                var create_db = true;
            }
            break;
        }
    }

    var configs = {
    	db_host : db_host,
		db_user : db_user,
		db_pswd : db_pswd,
		db_name : db_name,
		create_db : create_db,
		ami_host : ami_host,
		ami_user : ami_user,
		ami_pass : ami_pass,
		ami_port : ami_port,
		ami_timeout : ami_timeout,
		yandex_tts : yandex_tts,
		us1_link : us1_link,
		us1_api : us1_api,
		us2_link : us2_link,
		us2_api : us2_api
    }

    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: {
            method: "install",
            config: JSON.stringify(configs)
        },
        success: function(data) {
        	console.log(data.trim());
        	$.notify("Success: " + data.trim(), "success");
        },
        failure: function(errMsg) {
            $.notify("Critical! " + errMsg, "error");
        }
});

}