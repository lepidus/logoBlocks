# Bloco de patrocinadores — OJS 3.3

Exibe na barra lateral da revista logos de patrocinadores ou indexadores, cada um com link opcional. O próprio plugin redimensiona as imagens para **150 px de largura e altura proporcional**, sem precisar de um editor de imagens. Cada revista tem seu próprio título e sua própria lista de logos.

## Instalação e uso

1. Coloque a pasta `sponsorsBlock` em `plugins/blocks/` da instalação do OJS 3.3.
2. Entre como gerente da revista ou administrador. Em **Configurações → Website → Plugins → Plugins instalados → Plugins de bloco**, habilite **Bloco de patrocinadores**.
3. Abra **Configurações** no menu do plugin. O título padrão é **Index to:**. O campo de título é multilíngue, usando o mesmo seletor de idiomas dos formulários do OJS. Preencha o idioma principal da revista e, opcionalmente, as traduções. Na página pública, o título acompanha o idioma selecionado; na ausência de tradução, usa o idioma principal. Títulos cadastrados anteriormente são preservados no idioma principal. A grade mostra o nome, a prévia e o link de cada logo. Clique em **Adicionar logo**, informe o nome, selecione uma imagem e, se desejar, informe um link completo começando com `https://` ou `http://`. Salve o formulário do logo.
4. Repita a inclusão para cada logo. Os logos aparecem na ordem em que foram adicionados. O nome é usado como texto alternativo para acessibilidade.
5. Em **Configurações → Website → Aparência → Configuração → Barra lateral**, coloque o bloco na barra lateral e salve. A localização e a apresentação dependem do tema da revista.

Use as ações de cada linha da grade para **Editar** ou **Excluir** um logo. Na edição, é possível alterar o nome e o link e substituir a imagem, mantendo sua posição. Deixe o campo de arquivo vazio para preservar a imagem atual. A exclusão pede confirmação. Cada operação atualiza a grade e é salva imediatamente; o botão **Salvar** da tela principal grava o título e a opção de exibição acima do rodapé. A remoção também apaga o arquivo correspondente. Desabilitar o plugin oculta o bloco e preserva os dados. Um bloco sem logos não é exibido.

## Logos acima do rodapé

Nas configurações do plugin, marque **Exibir os logos também acima do rodapé** e salve. A opção vem desmarcada e é independente por revista. Quando ativada, mantém os logos na lateral e acrescenta uma faixa horizontal acima do rodapé, com quebra de linha em telas menores, o mesmo título traduzido e os mesmos links.

Mantenha o bloco na barra lateral em **Website → Aparência**: o OJS carrega este plugin quando renderiza a barra lateral. A faixa adicional aparece nas páginas que carregam o bloco e usam o rodapé padrão do OJS 3.3 (ou um tema que preserve `pkp_structure_footer_wrapper`). Páginas sem barra lateral não carregam a faixa. Desmarcar a opção remove somente a faixa adicional.

## Imagens e requisitos

- PHP com a extensão **GD** e suporte a PNG, JPEG e GIF.
- Diretório público da revista gravável pelo OJS. Os logos são públicos e ficam em `public/journals/<id>/` (ou no diretório configurado em `public_files_dir`).
- Entradas PNG, JPEG ou GIF de até **5 MB** e **12 milhões de pixels**. Os limites `upload_max_filesize` e `post_max_size` do PHP também se aplicam; este último deve comportar o arquivo e os demais campos.
- Os arquivos são decodificados e salvos como PNG. A transparência é preservada. GIFs animados são convertidos em imagens estáticas usando o primeiro quadro. SVG não é aceito.
- A largura final é de 150 px, inclusive para imagens menores; ampliar uma imagem pequena pode reduzir sua nitidez. A altura é proporcional, limitada a 3000 px. Em barras laterais mais estreitas, a exibição se adapta ao espaço disponível.
- Após um erro de validação, selecione o arquivo novamente antes de salvar.

Configurações disponíveis em português brasileiro e inglês. O plugin usa o controle de acesso do OJS para gerenciamento de plugins e exige requisição POST com token CSRF para salvar. O link opcional aceita apenas HTTP e HTTPS.

## Validação

Execute os testes de processamento de imagens e da grade:

```sh
php plugins/blocks/sponsorsBlock/tests/image.php
php plugins/blocks/sponsorsBlock/tests/grid.php
```

Para verificar o fluxo completo em uma instalação OJS 3.3: habilite o plugin, envie logos com e sem link, confira a proporção e os links na página pública, edite os dados e remova um logo. Confira também uma segunda revista para verificar a separação das configurações. Os testes da grade usam classes reais de formulário e GridHandler do OJS, com requisições e configurações simuladas em memória. Cobrem edição, validação de links, POST/CSRF, separação entre revistas, rotas de atualização e persistência/remoção dos arquivos a partir de imagens preparadas. Não executam upload HTTP nem autenticação no navegador e não substituem o teste do fluxo completo.

Licença: GNU GPL v3, conforme `docs/COPYING` do OJS.
