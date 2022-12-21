# ms-cobranca
Projeto Laravel que recebe uma lista csv de dados para geração de boletos e realiza a baixa da mesma
Projeto em Laravel 8.
A ideia do projeto foi a criação de é um sistema que recebe uma lista de cobranças no formato csv e processa a integração com serviço externo, gravando o resultado nas tabelas do BD, e no final do processo envia email de notificação para os cliente com o boleto gerado.


As rotas do projeto:

api/upload ->recebe a lista de csv para armazenamento no storage e enfileira o JOB. (insominia ou postman multpart Form com o nome do campo: listDebt)
api/confirmatiopn -> recebe retorno do webhook dpara baixar o boleto.
api/processCsvList ->em caso de falha no JOB ou caso queira ter outro processo chamando o JOB que processa a lista de csv



Formato da Lista de CSV:
name,governmentId,email,debtAmount,debtDueDate,debtId
John Doe1,11111111111,johndoe@meudominio.com.br,1000000.00,2022-10-12,8291
