# Blocos de logos — OJS 3.3

Exibe na barra lateral da revista logos de patrocinadores ou indexadores, cada um com link opcional. O plugin permite recortar as margens vazias das logos e redimensiona as imagens para **150 px de largura e altura proporcional**, sem precisar de um editor de imagens. Cada revista pode criar vários blocos, cada um com título, logos e opções de exibição próprios.

## Instalação e uso

1. Coloque a pasta `sponsors` em `plugins/blocks/` da instalação do OJS 3.3.
2. Entre como gerente da revista ou administrador. Em **Configurações → Website → Plugins → Plugins instalados → Plugins de bloco**, habilite **Blocos de logos**.
3. Abra **Configurações** no menu do plugin para ver a lista de blocos. O bloco de indexadores preserva as logos, o título e as opções já cadastrados. Clique no **lápis à direita do nome** para abrir sua tela de edição. Para criar outra categoria, clique em **Adicionar bloco**, informe o título e salve; depois, clique no lápis do novo bloco para incluir suas logos. O campo de título é multilíngue, usando o mesmo seletor de idiomas dos formulários do OJS. Preencha o idioma principal da revista e, opcionalmente, as traduções. Na página pública, o título acompanha o idioma selecionado; na ausência de tradução, usa o idioma principal. Títulos cadastrados anteriormente são preservados no idioma principal. A grade mostra o nome, a prévia e o link de cada logo. Clique em **Selecionar logo do indexador** para selecionar uma imagem da galeria e confirmar em **Salvar**, sem upload. Para enviar uma imagem própria, clique em **Adicionar nova logo**, informe o nome, selecione uma imagem e, se desejar, informe um link completo começando com `https://` ou `http://`. Salve o formulário do logo.
4. Repita a inclusão para cada logo. Novos logos são adicionados ao final da lista. Para reordenar, arraste a alça de pontos à esquerda de cada logo. A ordenação está disponível assim que as configurações abrem, sem botão de ativação, e é salva automaticamente ao soltar. A mesma sequência vale para a grade, a lateral e a faixa acima do rodapé. Também é possível focar a alça e usar as setas para cima e para baixo. Uma mensagem informa o resultado da gravação; em caso de falha, a ordem anterior é restaurada na tela. O nome é usado como texto alternativo para acessibilidade.
5. Em **Configurações → Website → Aparência → Configuração → Barra lateral**, coloque **Blocos de logos** na barra lateral e salve. Cada bloco aparece no local escolhido em sua personalização (lateral, rodapé ou ambos); blocos vazios ficam ocultos. A localização e a apresentação dependem do tema da revista.

Para ordenar os blocos, arraste a **alça de pontos à esquerda do nome**. A ordem é salva automaticamente e aplicada à lateral e ao rodapé. Também é possível focar a alça e usar as setas para cima e para baixo. Em caso de falha, a ordem anterior é restaurada na tela. Novos blocos entram no final da lista.

Na lista de blocos, a **pílula ao lado do lápis** ativa ou desativa a exibição daquele bloco na revista. Verde indica ativado; cinza indica desativado. A alteração é salva imediatamente e vale para a lateral e a faixa acima do rodapé, preservando as logos e configurações. Os blocos começam ativados. O lápis possui a dica **Editar**. Os blocos adicionais também exibem uma lixeira com a dica **Deletar**; após confirmação, ela remove o bloco, suas logos e os respectivos arquivos públicos. O bloco padrão de indexadores não pode ser deletado.

Use as ações de cada linha da grade para **Editar** ou **Excluir** um logo. Na edição, é possível alterar o nome e o link e substituir a imagem, mantendo sua posição. Deixe o campo de arquivo vazio para preservar a imagem atual. A exclusão pede confirmação. Cada operação atualiza a grade e é salva imediatamente; o botão **Salvar** da tela principal grava o título e a localização do bloco e a opção de escala de cinza. A remoção também apaga o arquivo correspondente. Desabilitar o plugin oculta o bloco e preserva os dados. Um bloco sem logos não é exibido.

## Galeria de logos

Somente o bloco padrão de **indexadores** possui a galeria preenchida. Os outros blocos têm a galeria vazia; inclua suas próprias imagens usando **Adicionar nova logo**. Os uploads compõem a lista de logos daquele bloco e não alimentam o catálogo de indexadores.

A galeria inclui **BASE, Diadorim, Dialnet, Dimensions, DOAJ, EBSCO, ErihPlus, Google Acadêmico, Latindex, LILACS, LiVRe, MIAR, Miguilim, OasisBR, OpenAIRE, OpenAlex, PsycINFO, PubMed/Medline, Redalyc, RePEc, ROAD, SciELO, Scopus, Sherpa Romeo e Web of Science**, além das demais logos do catálogo. Os nomes são exibidos em ordem alfabética; **Google Acadêmico** é a mesma entrada anteriormente chamada Google Scholar. O catálogo completo e as fontes das imagens estão em [logos/README.md](logos/README.md). Em **Selecionar logo do indexador**, selecione uma imagem e clique em **Salvar**. Ao selecionar uma logo, aparece abaixo da galeria um campo vazio com o exemplo “https://exemplo.org/perfil”. A galeria contém apenas o nome e a imagem de cada indexador; o editor é responsável por informar o endereço correspondente. Você pode informar o link da página da revista no indexador ou deixá-lo vazio antes de clicar em **Salvar**. O nome é preenchido automaticamente. Depois, use **Editar** para ajustar o nome ou o link.

O catálogo e as imagens acompanham o plugin, sem downloads durante o uso. Cada inclusão cria uma cópia no diretório público da revista, ao final da lista. Editar ou excluir essa cópia não altera a galeria nem outras revistas. **Adicionar nova logo** abre o formulário de upload já existente e não adiciona imagens ao catálogo compartilhado. Não é necessário criar uma tabela no banco: as escolhas usam as configurações já existentes do plugin.

As fontes das imagens e o procedimento para ampliar o catálogo estão em [logos/README.md](logos/README.md).

## Mudança do nome da pasta

A pasta é `sponsors` e o campo `<application>` de `version.xml` deve conter `sponsors`. Após renomear uma instalação anterior de `sponsorsBlock`, abra **Website → Plugins → Plugins instalados** para que o OJS registre o produto com o novo caminho. Depois, reabra **Website → Aparência → Configuração → Barra lateral**.

A classe `SponsorsBlockPlugin` e a chave interna `sponsorsblockplugin` foram mantidas para preservar a ativação, os logos, os títulos e a seleção do bloco na barra lateral. Não é necessário excluir o plugin ou recadastrar os logos.

## Logos acima do rodapé

Na seção **Personalização** de cada bloco, escolha uma opção e salve:

- **Exibir o bloco na lateral da revista.**
- **Exibir o bloco no rodapé da revista.**
- **Exibir o bloco em ambos os lugares.**

Novos blocos começam na lateral. Os blocos existentes preservam sua configuração anterior. No rodapé, as logos aparecem em uma faixa horizontal logo acima do rodapé padrão, com quebra de linha em telas menores. Uma única linha separa essa faixa do conteúdo da revista, sem linhas entre os blocos. Escolher somente o rodapé remove esse bloco da lateral. A pílula de ativação controla sua visibilidade em todos os locais.

Mantenha **Blocos de logos** habilitado na barra lateral em **Website → Aparência**, mesmo para a opção somente rodapé: o OJS carrega este plugin ao processar a barra lateral. A faixa aparece nas páginas que carregam o plugin e usam o rodapé padrão do OJS 3.3 (ou um tema que preserve `pkp_structure_footer_wrapper`).

## Logos em escala de cinza

Nas configurações do plugin, marque **Ativar escala de cinza nos logos** e salve. A opção vem desmarcada e vale somente para o bloco em edição. Os logos na lateral e acima do rodapé ficam cinzas e recuperam as cores ao passar o mouse ou focar seu link pelo teclado. Ao sair, voltam a ficar cinzas. O efeito é aplicado por CSS e preserva os arquivos das imagens. Desmarque e salve para manter as cores sempre visíveis.

## Recorte opcional e prévia

Ao enviar ou substituir uma imagem, uma prévia mostra como ela será salva. Marque **Recortar bordas** para visualizar e aplicar o recorte; desmarque para voltar à versão sem recorte. A opção vem desmarcada. A prévia não salva a logo: confirme no botão do formulário para gravar a versão escolhida. Na galeria de indexadores, as imagens aparecem nos próprios cartões, sem caixa de prévia adicional ou recorte de bordas; a imagem selecionada é apenas redimensionada.

Com a opção marcada, o plugin recorta as margens externas de cor uniforme, seja branca, preta ou qualquer outra cor. O fundo é identificado pela concordância de pelo menos três cantos, com tolerância a pequenas variações de compressão. O recorte acontece antes do redimensionamento. Em imagens com transparência, recorta somente as margens totalmente transparentes, preservando também desenhos brancos. O recorte mantém uma folga de um pixel da imagem original e a proporção do conteúdo restante.

Não remove o fundo dentro da logo. Fundos com gradientes, texturas ou cantos sem uma cor predominante podem não ser recortados. Imagens de cor única ou totalmente transparentes mantêm suas proporções. Elementos com cor muito próxima à do fundo podem ser considerados parte da margem. Os arquivos originais da galeria são preservados. Para aplicar o recorte a uma logo já cadastrada, substitua sua imagem em **Editar**; alterar apenas nome ou link preserva a imagem atual.

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
php plugins/blocks/sponsors/tests/image.php
php plugins/blocks/sponsors/tests/grid.php
php plugins/blocks/sponsors/tests/blocks.php
php plugins/blocks/sponsors/tests/gallery.php
php plugins/blocks/sponsors/tests/preview.php
```

Para verificar o fluxo completo em uma instalação OJS 3.3: habilite o plugin, envie logos com e sem link, confira a proporção e os links na página pública, edite os dados e remova um logo. Confira também uma segunda revista para verificar a separação das configurações. Os testes da grade usam classes reais de formulário e GridHandler do OJS, com requisições e configurações simuladas em memória. Cobrem edição, validação de links, POST/CSRF, separação entre revistas, rotas de atualização e persistência/remoção dos arquivos a partir de imagens preparadas. Não executam upload HTTP nem autenticação no navegador e não substituem o teste do fluxo completo.

Licença: GNU GPL v3, conforme `docs/COPYING` do OJS.
