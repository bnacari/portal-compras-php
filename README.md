# portal-compras-php
## Build / Deploy

PARA PUBLICAR EM HOMOLOGAÇÃO

. é necessário estar na branch STAGING

git checkout staging
git add .
git commit -m "XPTO"
git push

PARA SUBIR A APLICAÇÃO EM PRODUÇÃO, APÓS REALIZAR AS ALTERAÇÕES NECESSÁRIAS, BASTA EXECUTAR:
Está com o CI/CD CONFIGURADO, basta seguir os passos abaixo.

. Se estiver na branch STAGING, é necessário:

git checkout master
git merge staging

. e seguir os passos abaixo:

git add .
git commit -m "descrição"
git push

<!-- NÃO ESTÁ FUNCIONANDO A INTEGRAÇÃO ABAIXO -->

IR ATÉ O SITE ABAIXO E REALIZAR O DEPLOY MANUALMENTE

https://gitlab-monitor.sistemas.cesan.com.br/

O deploy ocorre automaticamente quando há push nas branchs *staging* (homologação) ou *master* (produção).