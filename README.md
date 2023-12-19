# visita-agendada-php
## Build / Deploy

docker build -t registry.sistemas.cesan.com.br/adm/visita-agendada:1.0.1 .

docker push registry.sistemas.cesan.com.br/adm/visita-agendada:1.0.1

docker-compose-prd -f docker/docker-compose-prd.yml --project-name visita-agendada up -d

PARA SUBIR A APLICAÇÃO EM PRODUÇÃO, APÓS REALIZAR AS ALTERAÇÕES NECESSÁRIAS, BASTA EXECUTAR:
Está com o CI/CD CONFIGURADO, basta seguir os passos abaixo.

git add --all

git commit -m "descrição"

git push

<!-- NÃO ESTÁ FUNCIONANDO A INTEGRAÇÃO ABAIXO -->

IR ATÉ O SITE ABAIXO E REALIZAR O DEPLOY MANUALMENTE

https://gitlab-monitor.sistemas.cesan.com.br/

O deploy ocorre automaticamente quando há push nas branchs *staging* (homologação)
ou *master* (produção).