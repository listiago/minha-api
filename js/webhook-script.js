jQuery(document).ready(function($) {
    // Envia o formulário de webhook via AJAX
    $('#webhook-form').submit(function(event) {
        event.preventDefault();

        var status = $('#status').val();
        var link = $('#link').val();

        console.log("🚀 Enviando dados para AJAX:", { status: status, link: link });

        $.ajax({
            url: ajax_object.ajaxurl,
            method: 'POST',
            data: {
                action: 'adicionar_webhook',
                status: status,
                link: link
            },
            success: function(response) {
                console.log("✅ Resposta do servidor:", response);
                if (response.success) {
                    obterWebhooks();
                    $('#status').val('');
                    $('#link').val('');
                } else {
                    alert(response.data ? response.data.message : 'Erro ao adicionar o webhook.');
                }
            },
            error: function(xhr, status, error) {
                console.error("❌ Erro na requisição AJAX:", status, error);
            }
        });
    });

    // Função para obter os webhooks cadastrados
function obterWebhooks() {
    $.ajax({
        url: ajax_object.ajaxurl,
        method: 'POST', // Mantendo como POST por segurança
        data: { action: 'obter_webhooks' },
        success: function(response) {
            console.log("✅ Webhooks carregados:", response);
            var list = $('#webhook-list');
            list.empty(); // Limpa a lista para evitar duplicações
    
            if (response.success && response.data.webhooks.length > 0) {
                response.data.webhooks.forEach(function(webhook) {
                    // Exibindo o status e link com a modificação solicitada
                    var linkExibido = webhook.webhook_url.length > 50 ? webhook.webhook_url.substring(0, 50) + '...' : webhook.webhook_url;
                    
                    list.append(
                        '<li data-id="' + webhook.id + '" class="webhook-item">' +
                        '<div class="webhook-info">' +
                        '<strong class="status" style="cursor: pointer;">' + webhook.status + '</strong>' + // Exibe só o status
                        '<div class="webhook-details" style="display: none;">' + // Esconde os botões e detalhes inicialmente
                        '<br><strong>Link:</strong> <input type="text" value="' + linkExibido + '" readonly class="webhook-link-display" style="width: 100%; max-width: 500px;">' +
                        '<div class="webhook-actions">' +
                        '<button class="edit-webhook">✏️ Editar</button>' +
                        '<button class="delete-webhook">🗑️ Excluir</button>' +
                        '<button class="test-webhook">Testar</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</li>'
                    );
                });
            } else {
                list.append('<li>Nenhum webhook cadastrado.</li>');
            }
        },
        error: function(xhr) {
            console.error("❌ Erro na requisição AJAX:", xhr.status, xhr.responseText);
        }
    });
}




    

    // Mostrar/ocultar detalhes ao clicar no status
    $(document).on('click', '.status', function() {
        $(this).closest('li').find('.webhook-details').toggle();
    });

    // Chama a função para obter webhooks ao carregar a página
    obterWebhooks();

    // Excluir webhook
    $(document).on('click', '.delete-webhook', function() {
        var webhookItem = $(this).closest('li');
        var webhookId = webhookItem.data('id');

        if (confirm('Você tem certeza que deseja excluir este webhook?')) {
            console.log("🚀 Enviando requisição para excluir webhook ID:", webhookId);

            $.ajax({
                url: ajax_object.ajaxurl,
                method: 'POST',
                data: {
                    action: 'excluir_webhook',
                    id: webhookId
                },
                success: function(response) {
                    console.log("✅ Resposta do servidor:", response);
                    if (response.success) {
                        webhookItem.remove();
                    } else {
                        alert(response.data ? response.data.message : 'Erro ao excluir o webhook.');
                    }
                },
                error: function(xhr) {
                    console.error("❌ Erro na requisição AJAX:", xhr.status, xhr.responseText);
                }
            });
        }
    });

    // Editar webhook
    $(document).on('click', '.edit-webhook', function() {
        var webhookItem = $(this).closest('li');
        var webhookId = webhookItem.data('id');
        var currentStatus = webhookItem.find('.status').text().trim();
        var currentLink = webhookItem.find('a').attr('href');

        var newStatus = prompt('Novo Status:', currentStatus);
        var newLink = prompt('Novo Link:', currentLink);

        if (newStatus && newLink) {
            $.ajax({
                url: ajax_object.ajaxurl,
                method: 'POST',
                data: {
                    action: 'editar_webhook',
                    id: webhookId,
                    status: newStatus,
                    link: newLink
                },
                success: function(response) {
                    console.log("✅ Resposta do servidor:", response);
                    if (response.success) {
                        obterWebhooks();
                    } else {
                        alert(response.data ? response.data.message : 'Erro ao editar o webhook.');
                    }
                },
                error: function(xhr) {
                    console.error("❌ Erro na requisição AJAX:", xhr.status, xhr.responseText);
                }
            });
        }
    });

// Testar webhook
$(document).on('click', '.test-webhook', function() {
    var webhookItem = $(this).closest('li');
    var webhookUrl = webhookItem.find('.webhook-link-display').val();

    var webhookStatus = webhookItem.find('.status').text().trim();

    if (!webhookUrl) {
        alert('Erro: URL do webhook não encontrada.');
        return;
    }

    console.log("🚀 Enviando requisição para testar webhook via PHP:", webhookUrl);

    // Não é necessário buscar user_id e token, pois test-webhook.php obtém esses dados
    $.ajax({
        url: '/wp-content/themes/api-web-hook/test-webhook.php',
        method: 'POST',
        data: {
            webhook_url: webhookUrl,
            webhook_status: webhookStatus
        },
        success: function(response) {
            console.log("✅ Webhook testado com sucesso:", response);
            alert(response.success ? response.success : 'Webhook testado!');
        },
        error: function(xhr) {
            console.error("❌ Erro ao testar webhook:", xhr.status, xhr.responseText);
            alert('Erro ao testar o webhook.');
        }
    });
});

jQuery(document).ready(function($) {
    function abrirEdicaoWhatsApp(numero) {
        $('#whatsapp-input').val(numero || '');
        $('#whatsapp-edicao').show();
        $('#whatsapp-input').focus();
    }

    function fecharEdicaoWhatsApp() {
        $('#whatsapp-edicao').hide();
    }

    $('#adicionar-whatsapp, #editar-whatsapp').on('click', function() {
        let numeroAtual = $('#whatsapp-display').attr('data-raw') || '';
        abrirEdicaoWhatsApp(numeroAtual);
    });

    $('#cancelar-whatsapp-input').on('click', function() {
        fecharEdicaoWhatsApp();
    });

    function formatarNumero(numero) {
        numero = numero.replace(/\D/g, ''); // Remove tudo que não for número
        if (numero.length > 11) {
            numero = numero.substring(0, 11);
        }

        if (numero.length === 11) {
            return `${numero.substring(0, 2)} 9 ${numero.substring(3, 7)}-${numero.substring(7)}`;
        }

        return numero;
    }

    $('#whatsapp-input').on('input', function() {
        let valor = $(this).val().replace(/\D/g, ''); // Remove caracteres não numéricos
        if (valor.length > 11) {
            valor = valor.substring(0, 11);
        }
        let formatado = formatarNumero(valor);
        $(this).val(formatado);
    });

    $('#salvar-whatsapp-input').on('click', function() {
        let whatsapp = $('#whatsapp-input').val().replace(/\D/g, ''); // Remove formatação antes de salvar
        let regex = /^\d{11}$/; 

        if (!regex.test(whatsapp)) {
            alert('Número inválido! O formato correto é 00 9 9999-9999.');
            return;
        }

        $.ajax({
            url: ajax_object.ajaxurl,
            method: 'POST',
            data: {
                action: 'salvar_whatsapp',
                whatsapp: whatsapp
            },
            success: function(response) {
                if (response.success) {
                    alert('WhatsApp salvo com sucesso!');
                    atualizarExibicaoWhatsApp(whatsapp);
                } else {
                    alert(response.error ? response.error : 'Erro ao salvar.');
                }
                fecharEdicaoWhatsApp();
            },
            error: function() {
                alert('Erro ao salvar número.');
                fecharEdicaoWhatsApp();
            }
        });
    });

    $('#excluir-whatsapp').on('click', function() {
        if (confirm('Tem certeza que deseja excluir o número salvo?')) {
            $.ajax({
                url: ajax_object.ajaxurl,
                method: 'POST',
                data: { action: 'excluir_whatsapp' },
                success: function(response) {
                    if (response.success) {
                        atualizarExibicaoWhatsApp('');
                    } else {
                        alert('Erro ao excluir.');
                    }
                },
                error: function() {
                    alert('Erro ao excluir número.');
                }
            });
        }
    });

    function atualizarExibicaoWhatsApp(numero) {
        if (numero.length === 11) {
            let formatted = formatarNumero(numero);
            $('#whatsapp-display').text(formatted).attr('data-raw', numero);
            $('#editar-whatsapp, #excluir-whatsapp').show();
            $('#adicionar-whatsapp').hide();
        } else {
            $('#whatsapp-display').text('Nenhum número cadastrado.').attr('data-raw', '');
            $('#editar-whatsapp, #excluir-whatsapp').hide();
            $('#adicionar-whatsapp').show();
        }
    }

    function carregarWhatsApp() {
        $.ajax({
            url: ajax_object.ajaxurl,
            method: 'POST',
            data: { action: 'obter_whatsapp' },
            success: function(response) {
                if (response.success && response.data.whatsapp) {
                    atualizarExibicaoWhatsApp(response.data.whatsapp);
                } else {
                    atualizarExibicaoWhatsApp('');
                }
            },
            error: function() {
                console.error('Erro ao obter o WhatsApp.');
            }
        });
    }

    carregarWhatsApp();
});
});