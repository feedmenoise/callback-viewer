$(document).ready(function() {
    feather.replace();
});

class Client {
    constructor(client_div) {
        this.number = client_div.getElementsByClassName("number")[0].textContent;
        this.login_div = client_div.getElementsByClassName("login")[0];
        this.company = client_div.getElementsByClassName("company")[0].textContent;
    }
}

class Peer {
    constructor(peer_div) {
        this.number = peer_div.getElementsByClassName("number")[0].textContent;
        this.name = peer_div.getElementsByClassName("name")[0];
    }
}

const clients = document.getElementsByClassName("client");
for (let client_div of clients) {
    let c = new Client(client_div);
    getInfo(c);
}

const peers = document.getElementsByClassName("peer");
for (let peer_div of peers) {
    let p = new Peer(peer_div);
    getPeerName(p);
}

async function getPeerName(p) {
    if (p.number != "") {

        $.ajax({
            type: "POST",
            url: "inc/ajax.php",
            data: {
                method: "getPeerName",
                peer: JSON.stringify(p.number)
            },
            success: function(data) {
                p.name.append(data);
            }});
    }
}

function getInfo(c) {
    getAbonInfo(c.number, c.login_div, c.company);
}

function addLoginToPage(login, div, id, company) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: {
            method: "getLink",
            id: JSON.stringify(id),
            company: company
        },
        success: function(data) {
      let link = document.createElement('a');
      var linkText = document.createTextNode(login + "\r\n");
      link.setAttribute('href', data);
      link.setAttribute('target', '_blank');
      link.appendChild(linkText);
      div.appendChild(link);
  }
});
}

async function getAbonInfo(number, div, company) {
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        dataType: "json",
        data: {
            method: "getAbon",
            number: JSON.stringify(number),
            company: JSON.stringify(company)
        },
        success: function(data) {
            console.log(data);
            for(var i in data) {
              let link = document.createElement('a');
              var linkText = document.createTextNode(data[i].login + "\r\n");
              link.setAttribute('href', data[i].url_to_us);
              link.setAttribute('target', '_blank');
              link.appendChild(linkText);
              div.appendChild(link);
          }
        }
    });
}

function dongleReset(dongle) {
    $.notify(dongle + " отправлен в перезагрузку.", "info");
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: {
            method: "dongleReset",
            dongle: JSON.stringify(dongle)
        },
        success: function(data) {
            if (data.trim() != '') {
                $.notify("Не удалось перезагузить модем " + ": " + data, "error");
            } else {
                $.notify("Модем " + " перезагружается", "success");
            }
        },
        failure: function(errMsg) {
            $.notify("Critical! " + errMsg, "error");
        }
    });
}

function applyMessage(text, file) {

    var radios = document.getElementsByName('radio');

    for (var i = 0, length = radios.length; i < length; i++) {
        if (radios[i].checked) {
            if (radios[i].value == "gigabit") {
                var context = "ivr_avariynoe";
            }
            if (radios[i].value == "gorodok") {
                var context = "ivr_avariynoe_gorodok";
            }
            break;
        }
    }
    //alert(context);
    var message = {
        "text": text,
        "filename": file,
        "context": context
    };
    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: {
            method: "applyMessage",
            message: JSON.stringify(message)
        },
        success: function(json_response) {
            $.notify("Сообщение установлено", "success");
            $("#applyMessageBtn").prop("disabled", true);
            $("#applyMessageBtn").removeClass("btn-success").addClass("btn-outline-secondary");

            var response = $.parseJSON(json_response);

            $.notify(response.context, "info");

            if (response.context == "ivr_avariynoe") {

                $("div.current-message-status.Gigabit").html("Включено");
                $("div.current-message-text.Gigabit").append(response.text);
                $(".player.Gigabit").removeClass('hide_content');
                $(".player.Gigabit").attr("src", response.url);
                $(".Gigabit.clearMessageBtn").removeClass("hide_content");
                $(".Gigabit.clearMessageBtn").show();

                //alert("must add to gigabit");
            }
            if (response.context == "ivr_avariynoe_gorodok") {

                $("div.current-message-status.Gorodok").html("Включено");
                $("div.current-message-text.Gorodok").append(response.text);
                $(".player.Gorodok").removeClass('hide_content');
                $(".player.Gorodok").attr("src", response.url);
                $(".Gorodok.clearMessageBtn").removeClass("hide_content");
                $(".Gorodok.clearMessageBtn").show();

                //alert("must add to gorodok");
            }
        },
        failure: function(errMsg) {
            $.notify("Critical! " + errMsg, "error");
        }
    });
}

//РИСУЕМ ТАБЛИЦУ ШАБЛОНОВ

$(document).ready(function() {
    $('#template-tab').on('click', function() {
        $('div.message-container').empty();
        $('div.message-container').append('<center><button type="button" class="message-gigabit"><img src="./img/logo-gigabit-sm.png"></button>' +
            '<button type="button" class="message-gorodok"><img src="./img/logo-gorodok-sm.png"></button></center>');


        $(document).ready(function() {
            $('.message-gigabit').on('click', function() {
                $('div.message-container').empty();
                $('div.message-container').append('<center><img src="./img/logo-gigabit-sm.png"> ');
                $('div.message-container').append('<ul class="message-responsive-table"></ul>');
                generateTemplateTable('1');
            });

            $('.message-gorodok').on('click', function() {
                $('div.message-container').empty();
                $('div.message-container').append('<center><img src="./img/logo-gorodok-sm.png"> ');
                $('div.message-container').append('<ul class="message-responsive-table"></ul>');
                generateTemplateTable('2');
            });
        });



        function generateTemplateTable(company) {
            $.ajax({
                type: 'POST',
                url: 'inc/ajax.php',
                dataType: "json",
                data: {
                    method: "templateMessages",
                    company: company
                },
                success: function(json) {
                    if (company == 1) company_class = "Gigabit";
                    else if (company == 2) company_class = "Gorodok";

                    var globalFlag = 0;
                    $.each(json, function(index, element) {
                        $('ul.message-responsive-table').append('<li class="message-table-row ' + element.id + '"></div>')
                        $('.message-table-row.' + element.id).append('<div class="message-col message-col-1" >' + element.text + '</div>');
                        $('.message-table-row.' + element.id).append('<div class="message-col message-col-2" >' +
                            '<button type="button" class="btn btn-sm btn-outline-info play-template" data-toggle="modal" data-target="#exampleModalCenter" data-id="' + element.id + '"><span data-feather="play"></span></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-success applyMessageTemplate ' + element.id + '" data-id="' + element.id + '"><span data-feather="send"></span></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger deleteTemplate ' + element.id + '" data-id="' + element.id + '"><span data-feather="trash-2"></span></button>' +
                            '<button type="button" class="btn btn-sm btn-outline-danger clearMessageBtnTemplate ' + element.id + ' hide_content ' + company_class + ' " data-id="' + element.id + '"><span data-feather="slash"></span></button></div>');

                        if (element.flag == 1) {
                            $('.message-table-row.' + element.id).css("background", "#7FFFD4");
                            $('.clearMessageBtnTemplate').removeClass('hide_content');
                            $('.clearMessageBtnTemplate').show();
                            $('.deleteTemplate').hide();
                            globalFlag = 1;
                        }
                    });

                    if (globalFlag == 1) {
                        $('.applyMessageTemplate').hide();
                    }


                    //модалка с аудиозаписью
                    $('.play-template').on('click', function() {
                        var id = $(this).data('id');
                        $('.modal-body').html('loading');
                        console.log(id);
                        $.ajax({
                            type: 'POST',
                            url: 'inc/ajax.php',
                            data: {
                                method: "playTemplateModal",
                                id: id
                            },
                            success: function(data) {
                                console.log(data);
                                $('.modal-content').html(data);
                            },
                            failure: function(errMsg) {
                                $.notify("Critical! " + errMsg, "error");
                            }
                        });
                    });

                    //удаляем шаблон
                    $('.deleteTemplate').on('click', function() {
                        var id = $(this).data('id');
                        $.ajax({
                            type: "POST",
                            url: "inc/ajax.php",
                            data: {
                                method: "deleteTemplate",
                                id: id
                            },
                            success: function(data) {
                                if (data.trim() == '') {
                                    $.notify("Шаблон удален", "success");
                                    $('.message-table-row.' + id).remove();
                                } else {
                                    $.notify(data.trim(), "info");
                                }
                            },
                            failure: function(errMsg) {
                                $.notify("Critical! " + errMsg, "error");
                            }
                        });
                    });

                    //выключаем текущее сообщение
                    $('.clearMessageBtnTemplate.Gigabit').on('click', function() {
                        var id = $(this).data('id');
                        var message = {
                            "context": "ivr_avariynoe"
                        };

                        clearMessageTemplate(id, message, company_class);

                    });

                    $('.clearMessageBtnTemplate.Gorodok').on('click', function() {
                        var id = $(this).data('id');
                        var message = {
                            "context": "ivr_avariynoe_gorodok"
                        };

                        clearMessageTemplate(id, message, company_class);

                    });

                    function clearMessageTemplate(id, message, company) {
                        $.ajax({
                            type: "POST",
                            url: "inc/ajax.php",
                            data: {
                                method: "clearMessage",
                                message: JSON.stringify(message)
                            },
                            success: function() {
                                $.notify("Сообщение выключено!", "success");
                                $("div.current-message-status." + company_class).html("Выключено");
                                $("div.current-message-text." + company_class).html("");
                                $(".player." + company_class).addClass('hide_content');
                                $(".player." + company_class).attr("src", "");
                                $("button.applyMessageTemplate").show();
                                $(".clearMessageBtnTemplate").hide();
                                $(".clearMessageBtn.current." + company_class).hide();
                                $(".deleteTemplate").show();
                                $('.message-table-row').css("background", "#FFF");
                            },
                            failure: function(errMsg) {
                                $.notify("Critical! " + errMsg, "error");
                            }
                        });
                    }

                    //включаем сообщение из шаблона
                    $('.applyMessageTemplate').on('click', function() {
                        var id = $(this).data('id');
                        $.ajax({
                            type: "POST",
                            url: "inc/ajax.php",
                            data: {
                                method: "applyFromTemplate",
                                id: id
                            },
                            success: function(json_response) {
                                $.notify("Сообщение установлено", "success");
                                var response = $.parseJSON(json_response);
                                $("div.current-message-status." + company_class).html("Включено");
                                $("div.current-message-text." + company_class).empty();
                                $("div.current-message-text." + company_class).append(response.text);
                                $("button.applyMessageTemplate").hide();
                                $(".player." + company_class).removeClass('hide_content');
                                $(".player." + company_class).attr("src", response.url);
                                $(".clearMessageBtnTemplate." + id).removeClass("hide_content");
                                $(".clearMessageBtnTemplate." + id).show();
                                $(".clearMessageBtn.current." + company_class).removeClass("hide_content");
                                $(".clearMessageBtn.current." + company_class).show();
                                $(".deleteTemplate." + id).hide();
                                $('.message-table-row').css("background", "#FFF");
                                $('.message-table-row.' + id).css("background", "#7FFFD4");
                            },
                            failure: function(errMsg) {
                                $.notify("Critical! " + errMsg, "error");
                            }
                        });
                    });

                    feather.replace();
                },
                failure: function(errMsg) {
                    console.log("reutrn with failure from ajax");
                    $.notify("Critical! " + errMsg, "error");
                }
            });

}

});
});


$(document).ready(function() {
    $('.play-rec').on('click', function() {
        var id = $(this).data('id');
        $('.modal-body').html('loading');
        console.log(id);
        $.ajax({
            type: 'POST',
            url: 'inc/ajax.php',
            data: {
                method: "playRecModal",
                id: id
            },
            success: function(data) {
                console.log(data);
                $('.modal-content').html(data);
            },
            failure: function(errMsg) {
                $.notify("Critical! " + errMsg, "error");
            }
        });
    });
});

function setPlaySpeed(speed) { 
  var aid = document.getElementById("audio");
  aid.playbackRate = speed;
} 

function saveAsTemplate(text, file) {
    var radios = document.getElementsByName('radio');

    for (var i = 0, length = radios.length; i < length; i++) {
        if (radios[i].checked) {
            if (radios[i].value == "gigabit") {
                company = 1;
            }
            if (radios[i].value == "gorodok") {
                company = 2;
            }
            break;
        }
    }

    var message = {
        "text": text,
        "filename": file,
        "company": company
    };

    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: {
            method: "saveMessage",
            message: JSON.stringify(message)
        },
        success: function(data) {
            if (data.trim() == '') {
                $.notify("Сообщение сохранено", "success");
                $("#saveMessageBtn").prop("disabled", true);
                $("#saveMessageBtn").removeClass("btn-success").addClass("btn-outline-secondary");
            } else {
                $.notify(data.trim(), "info");
            }
        },
        failure: function(errMsg) {
            $.notify("Critical! " + errMsg, "error");
        }
    });
}

$('#createMessageForm').on('submit', function() {
    var text = $("#message").val();
    $.notify("Синтезируется: " + text, "info");
    $("#message").prop("disabled", true);
    $("#submitMessage").prop("disabled", true);
    $("#submitMessage").removeClass("btn-sm btn-success").addClass("hide_content");
    $("#loadingMessage").removeClass("hide_content");
    $("#loadingMessage").addClass("d-flex");
    $("#createMessageForm").addClass("hide_content");

    var message = {
        "text": text
    };

    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: {
            method: "createMessage",
            message: JSON.stringify(message)
        },
        success: function(json_response) {
            $.notify("Запись получена!", "success");
            $("#loadingMessage").addClass("hide_content");
            $("#loadingMessage").removeClass("d-flex");

            var response = $.parseJSON(json_response);

            $('#createMessageForm').removeClass("hide_content");
            $("#message").val(response.text);
            $('#createMessageForm').append('<div class="audio"><audio controls preload="none"><source src="' + response.url + '" type="audio/wav"></audio><br></div>');
            $('#createMessageForm').append('<div class="form_toggle">  <div class="form_toggle-item item-1"><input id="fid-1" type="radio" name="radio" value="gigabit" checked><label for="fid-1">Gigabit</label></div><div class="form_toggle-item item-2"><input id="fid-2" type="radio" name="radio" value="gorodok"><label for="fid-2">Gorodok</label></div></div>');
            $('#createMessageForm').append('<button type="button" id="editMessageBtn" class="btn btn-sm btn-success">Изменить</button> ');
            $('#createMessageForm').append('<button type="button" id="applyMessageBtn" class="btn btn-sm btn-success" onclick="applyMessage(\'' + response.text + '\',\'' + response.file_name + '\')">Применить</button> ');
            $('#createMessageForm').append('<button type="button" id="saveMessageBtn" class="btn btn-sm btn-success" onclick="saveAsTemplate(\'' + response.text + '\',\'' + response.file_name + '\')">Сохранить как шаблон</button> ');

            $('#editMessageBtn').on('click', function() {
                $("#message").prop("disabled", false);
                $("#submitMessage").prop("disabled", false);
                $("#submitMessage").removeClass("hide_content").addClass("btn-sm btn-success");
                $("#editMessageBtn").remove();
                $("#applyMessageBtn").remove();
                $("#saveMessageBtn").remove();
                $("div.audio").remove();
                $("div.form_toggle").remove();
            });
        },
        failure: function(errMsg) {
            $.notify("Critical! " + errMsg, "error");
        }
    });

    return false;
});


$('.clearMessageBtn.current.Gorodok').on('click', function() {
    var message = {
        "context": "ivr_avariynoe_gorodok"
    };

    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: {
            method: "clearMessage",
            message: JSON.stringify(message)
        },
        success: function() {
            $.notify("Gorodok: Сообщение выключено!", "success");
            $("div.current-message-status.Gorodok").html("Выключено");
            $("div.current-message-text.Gorodok").empty();
            $(".player.Gorodok").addClass('hide_content');
            $(".player.Gorodok").attr("src", "")
            $(".clearMessageBtn.Gorodok").hide();
        },
        failure: function(errMsg) {
            $.notify("Critical! " + errMsg, "error");
        }
    });
});

$('.clearMessageBtn.current.Gigabit').on('click', function() {
    var message = {
        "context": "ivr_avariynoe"
    };

    $.ajax({
        type: "POST",
        url: "inc/ajax.php",
        data: {
            method: "clearMessage",
            message: JSON.stringify(message)
        },
        success: function() {
            $.notify("Gigabit: Сообщение выключено!", "success");
            $("div.current-message-status.Gigabit").html("Выключено");
            $("div.current-message-text.Gigabit").empty();
            $(".player.Gigabit").addClass('hide_content');
            $(".player.Gigabit").attr("src", "")
            $(".clearMessageBtn.Gigabit").hide();
        },
        failure: function(errMsg) {
            $.notify("Critical! " + errMsg, "error");
        }
    });
});