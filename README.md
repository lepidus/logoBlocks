# Blocos de logos — OJS 3.3

O plugin **Blocos de logos** permite apresentar logos de patrocinadores, apoiadores ou indexadores nas páginas públicas da revista, na **barra lateral**, em uma **faixa acima do rodapé** ou nos dois lugares. Cada logo pode ter um link e uma descrição curta.

Por exemplo, a revista pode criar um bloco chamado “Indexadores” com logos e links para seus perfis nas bases, e outro chamado “Apoio” com as instituições que a apoiam. Cada revista configura seus próprios blocos, títulos, imagens e ordem de apresentação.

## O que o plugin faz

- Permite criar vários blocos, com títulos traduzidos para os idiomas da revista.
- Oferece uma galeria de logos incluída no plugin e permite enviar imagens próprias.
- Redimensiona as imagens para 150 px de largura, mantendo a proporção, e oferece recorte opcional de margens no envio de imagens.
- Permite ordenar, editar, ocultar e excluir blocos e logos.
- Oferece opções por bloco para localização, exibição das descrições e efeito de escala de cinza.

**A galeria é um catálogo de imagens, não uma integração com os indexadores.** Selecionar uma logo não cadastra a revista em uma base, não confirma sua indexação e não busca automaticamente o endereço de seu perfil. Cabe à equipe da revista escolher as logos e informar os links correspondentes.

O plugin não envia e-mails, não altera o fluxo de submissão ou avaliação e não cria tarefas agendadas. **Não é necessário configurar cron.** As imagens são processadas ao preparar a prévia ou salvar uma logo; os blocos são exibidos quando o OJS renderiza as páginas públicas.

## Requisitos e instalação

Esta versão é destinada ao **OJS 3.3**. O servidor precisa ter:

- PHP com a extensão **GD** e suporte a PNG, JPEG e GIF.
- Diretório público da revista gravável pelo OJS.

Para instalar e habilitar:

1. Coloque a pasta `logoBlocks`, com os arquivos do plugin, em `plugins/blocks/` da instalação do OJS. O arquivo de versão deve ficar em `plugins/blocks/logoBlocks/version.xml`.
2. Entre no OJS como gerente da revista ou administrador e acesse a revista que deseja configurar.
3. Em **Configurações → Website → Plugins → Plugins instalados → Plugins de bloco**, habilite **Blocos de logos**.
4. Em **Configurações → Website → Aparência → Configuração → Barra lateral**, inclua **Blocos de logos** na barra lateral e salve.

**O passo 4 também é necessário para exibir logos somente acima do rodapé.** O OJS carrega este plugin ao processar a barra lateral. A opção de localização de cada bloco determina onde ele será mostrado.

Habilitar o plugin não cria blocos nem adiciona logos automaticamente. Siga os passos abaixo para cadastrar o primeiro bloco.

## Criar o primeiro bloco

1. No menu do plugin **Blocos de logos**, abra **Configurações**.
2. Clique em **Adicionar bloco**.
3. Preencha o **Título do bloco**, por exemplo, “Indexadores”. Use o seletor de idiomas do OJS para preencher o idioma principal da revista e, opcionalmente, suas traduções. O limite é de 200 caracteres por idioma.
4. Em **Personalização do bloco**, escolha onde exibi-lo. Por padrão, novos blocos aparecem na lateral, com descrições ocultas e imagens coloridas.
5. Clique em **Salvar**. Na lista de blocos, clique no lápis (**Editar**) para abrir o bloco criado e adicionar suas logos.
6. Inclua ao menos uma logo por uma das opções descritas a seguir e confira o resultado na página pública da revista.

O título público acompanha o idioma selecionado pelo visitante; se não houver tradução, usa o idioma principal da revista. Títulos antigos, cadastrados antes do suporte a traduções, são preservados no idioma principal. **Blocos sem logos não aparecem na página pública.**

## Adicionar logos

### Escolher uma logo da galeria

1. Abra a edição do bloco e clique em **Galeria de logos**.
2. Selecione uma imagem. Abaixo da galeria, aparecem os campos de link e descrição.
3. Se desejar, informe o endereço completo da página da revista no indexador, começando com `https://` ou `http://`, e uma descrição de até 100 caracteres. Ambos são opcionais.
4. Clique em **Salvar**. A logo entra no final da lista, com o nome e a sigla definidos pelo catálogo. Depois, use **Editar** para ajustar esses dados.

A galeria inclui, entre outros, DOAJ, Google Scholar, Latindex, SciELO, Scopus e Web of Science. Os cartões mostram a sigla, quando disponível, ou o nome, e são ordenados pelo nome completo. A lista de entradas está em [logos/catalog.json](logos/catalog.json).

O catálogo e suas imagens acompanham o plugin; não há download de imagens de serviços externos durante o uso. Todos os blocos compartilham esse catálogo, mas cada inclusão salva uma cópia processada no diretório público da revista. Editar ou excluir uma logo cadastrada não altera o catálogo nem as cópias de outros blocos ou revistas.

### Enviar uma imagem própria

1. Abra a edição do bloco e clique em **Adicionar logo**.
2. Preencha **Título da logo** e selecione a imagem PNG, JPEG ou GIF.
3. Preencha, se desejar, a sigla, o link e a descrição.
4. Confira a prévia. Se precisar remover margens vazias, marque **Recortar bordas**.
5. Salve o formulário da logo.

As imagens enviadas são adicionadas apenas ao bloco em edição; elas não passam a fazer parte da galeria compartilhada.

### Para que serve cada campo

| Campo | Uso |
| --- | --- |
| **Título da logo** | Nome obrigatório, com até 200 caracteres. É usado como texto alternativo da imagem e como dica ao passar o mouse. |
| **Sigla (opcional)** | Até 30 caracteres. Aparece em destaque na lista administrativa, ao lado do nome completo; não substitui o texto alternativo da imagem. |
| **Link (opcional)** | Endereço HTTP ou HTTPS aberto em uma nova aba ao clicar na imagem. Sem link, a logo é apenas uma imagem. |
| **Descrição (opcional)** | Texto simples de até 100 caracteres, exibido abaixo da imagem quando a exibição de descrições estiver habilitada para aquele local. Não aceita formatação HTML. |

O título do bloco é multilíngue. Os campos de cada logo têm um único valor, usado em todos os idiomas da página.

## Personalizar a exibição

Abra o lápis (**Editar**) do bloco, ajuste as opções em **Personalização do bloco** e clique em **Salvar**.

### Localização: lateral, rodapé ou ambos

No campo **Selecione onde exibir o bloco**, escolha:

| Opção | Resultado na página pública |
| --- | --- |
| **Exibir na lateral da revista** | Logos em uma lista na barra lateral. |
| **Exibir no rodapé da revista** | Logos em uma faixa horizontal acima do rodapé padrão, sem exibir esse bloco na lateral. |
| **Exibir em ambos os lugares** | O mesmo bloco aparece na lateral e acima do rodapé. |

A faixa acima do rodapé quebra as logos em novas linhas quando falta espaço. Uma linha separa o conjunto de blocos do conteúdo da revista, sem separadores entre os blocos.

A apresentação depende do tema. A faixa aparece nas páginas que carregam o plugin pela barra lateral e usam o rodapé padrão do OJS 3.3, ou um tema que preserve o elemento com a classe `pkp_structure_footer_wrapper`. Mantenha **Blocos de logos** selecionado na configuração da barra lateral mesmo ao escolher apenas o rodapé.

### Descrições abaixo das logos

Em **Exibição da descrição dos logos**, escolha **Não exibir**, **Exibir na lateral da revista**, **Exibir no rodapé da revista** ou **Exibir em ambos os lugares**.

Essa opção controla somente o texto abaixo das imagens; ela não muda a localização do bloco. Por exemplo, uma descrição configurada apenas para o rodapé não aparece se o bloco estiver configurado apenas para a lateral. Logos sem descrição não exibem texto adicional.

As descrições ficam ocultas por padrão. Selecionar **Não exibir** preserva os textos cadastrados.

### Escala de cinza

Marque **Ativar escala monocromática, ficam coloridas ao interagir.** e salve. As logos daquele bloco ficam em escala de cinza na lateral e acima do rodapé, recuperando as cores ao passar o mouse ou focar seu link pelo teclado. Ao sair, voltam a ficar cinzas.

O efeito usa CSS e não modifica os arquivos das imagens. Desmarque a opção e salve para manter as cores sempre visíveis.

## Editar, ordenar e salvar

Para editar uma logo, use **Editar** na linha correspondente. Você pode alterar seus textos e link ou substituir a imagem. **Deixe o campo de arquivo vazio para preservar a imagem atual.** A edição mantém a posição da logo na lista; substituir a imagem remove o arquivo público anterior.

Para reordenar logos ou blocos, arraste a **alça de pontos à esquerda** da linha. Também é possível focar a alça pelo teclado e usar as setas para cima e para baixo. A ordem é salva automaticamente ao soltar ou mover pelo teclado e vale tanto para a lateral quanto para a faixa acima do rodapé. Novos itens entram no final da lista. Em caso de falha na gravação, a tela restaura a ordem anterior e informa o erro.

As ações não dependem todas do mesmo botão **Salvar**:

| Ação | Quando é gravada |
| --- | --- |
| Criar ou editar título e personalização de um bloco | Ao clicar em **Salvar** no formulário do bloco. |
| Adicionar ou editar uma logo | Ao salvar o formulário daquela logo. |
| Reordenar logos ou blocos | Automaticamente, ao mudar a ordem. |
| Ativar ou desativar um bloco pela pílula | Imediatamente. |
| Excluir uma logo ou um bloco | Após confirmar a exclusão. |

Fechar o formulário do bloco sem salvar não desfaz operações já gravadas nas logos, na ordem ou na visibilidade.

## Ocultar, desativar ou excluir: o que acontece?

| Ação | Efeito | Dados preservados? |
| --- | --- | --- |
| Desativar a pílula de um bloco | Oculta aquele bloco na lateral e acima do rodapé. Os demais continuam conforme suas configurações. | Sim: logos, textos, ordem e opções. |
| Desabilitar o plugin em **Plugins instalados** | Oculta todos os blocos do plugin naquela revista. Não altera submissões, avaliações ou e-mails do OJS. | Sim: configurações e arquivos das logos permanecem armazenados. |
| Retirar **Blocos de logos** da configuração da barra lateral | Impede seu carregamento pela barra lateral, afetando também a faixa acima do rodapé. | Sim. |
| Excluir uma logo | Remove a logo do bloco e seu arquivo público. | Não para a logo excluída. |
| Deletar um bloco pela lixeira | Remove o bloco, suas configurações, todas as suas logos e os arquivos públicos correspondentes. | Não para o bloco excluído. |

A pílula ao lado do lápis fica **verde quando o bloco está ativado** e **cinza quando está desativado**. Novos blocos começam ativados. Para voltar a exibir um bloco oculto, reative sua pílula; se tiver desabilitado o plugin, habilite-o novamente e confira sua seleção na barra lateral.

**Ocultar um bloco ou desabilitar o plugin não torna seus arquivos privados.** As imagens continuam no diretório público e podem ser acessadas por quem tiver seus endereços. Para remover uma logo e seu arquivo pelo plugin, use a exclusão.

## Recorte, prévia e tamanho das imagens

Ao enviar ou substituir uma imagem, a prévia mostra como ela será salva. **Recortar bordas** vem desmarcado: marque para conferir o recorte ou desmarque para voltar à versão sem recorte. Gerar a prévia não salva a logo; é necessário confirmar o formulário.

O recorte remove margens externas totalmente transparentes ou de cor uniforme, como branco ou preto, antes do redimensionamento. Para fundos opacos, o plugin identifica uma cor predominante em pelo menos três cantos, com tolerância a pequenas variações de compressão. Preserva uma folga de um pixel da imagem original e a proporção do conteúdo restante. Em imagens com transparência, remove apenas margens totalmente transparentes, preservando desenhos brancos.

O recorte não remove o fundo interno da logo. Gradientes, texturas e cantos sem cor predominante podem impedir o recorte; elementos de cor muito próxima à do fundo podem ser interpretados como margem. Imagens de cor única ou totalmente transparentes mantêm suas proporções.

Na galeria, os cartões já mostram as imagens: não há recorte nem prévia adicional. A imagem escolhida é apenas redimensionada. Para recortar uma logo já cadastrada, abra **Editar** e envie novamente a imagem com **Recortar bordas** marcado. Alterar somente os textos ou o link preserva a imagem existente.

### Formatos e limites

- São aceitos **PNG, JPEG e GIF**, de até **5 MB** e **12 milhões de pixels** (largura × altura). SVG não é aceito.
- Os limites `upload_max_filesize` e `post_max_size` do PHP também se aplicam. O segundo deve comportar o arquivo e os demais campos do formulário.
- As imagens são convertidas em **PNG**, preservando a transparência. GIFs animados tornam-se imagens estáticas com o primeiro quadro.
- O arquivo salvo tem **150 px de largura** e altura proporcional. Imagens menores também são ampliadas, o que pode reduzir a nitidez. Imagens cuja altura final ultrapasse **3000 px** são rejeitadas.
- Na página pública, a altura de exibição é limitada a **100 px**, com redução proporcional da largura. Por isso, uma logo alta pode aparecer com menos de 150 px de largura. A exibição também se adapta ao espaço disponível na lateral.
- Depois de um erro de validação, selecione o arquivo novamente antes de salvar.

## Armazenamento e separação entre revistas

As configurações são salvas por revista nas configurações de plugins do OJS; o plugin não cria uma tabela própria. Os arquivos processados ficam em `public/journals/<id>/`, ou no caminho correspondente definido por `public_files_dir`.

Cada bloco mantém sua própria lista de logos e opções. As operações em uma revista não alteram os blocos de outras revistas. A única parte compartilhada é o catálogo de imagens que acompanha os arquivos do plugin.

O gerenciamento usa o controle de acesso do OJS para plugins. As operações de gravação exigem requisições POST com token CSRF.

## Problemas comuns

| Situação | O que verificar |
| --- | --- |
| Nenhum bloco aparece | Confira se o plugin está habilitado e selecionado na barra lateral, se a pílula do bloco está ativada e se ele contém ao menos uma logo. |
| O bloco aparece na lateral, mas não acima do rodapé | Confira a localização escolhida e salva no bloco e se o tema usa o elemento `pkp_structure_footer_wrapper`. |
| A descrição não aparece | Preencha a descrição da logo e habilite sua exibição para um local onde o bloco também esteja visível. |
| A imagem não pode ser salva | Confira formato, tamanho, quantidade de pixels, extensão GD, limites de upload do PHP e permissão de gravação do diretório público. Selecione o arquivo novamente após corrigir o problema. |
| A logo não abre um endereço | Edite a logo e informe um link HTTP ou HTTPS. A galeria não preenche o endereço automaticamente. |
| A ordem não foi salva | Reabra as configurações para atualizar a lista e tente novamente. |

## Atualização de instalações com o nome antigo

Esta orientação se aplica a quem já usava o plugin com o nome antigo de pasta.

A pasta de instalação é `logoBlocks`, e o campo `<application>` de `version.xml` deve conter `logoBlocks`. Após renomear a pasta de uma instalação anterior de `sponsorsBlock` ou `sponsors` para `logoBlocks`, abra **Website → Plugins → Plugins instalados** para que o OJS registre o produto com o novo caminho. Depois, reabra **Website → Aparência → Configuração → Barra lateral** e confira a seleção do plugin.

A classe é `LogoBlocksPlugin`, e a chave interna é `logoblocksplugin`. Essa chave corresponde ao nome da classe em minúsculas, como exige a busca de plugins habilitados do OJS 3.3. A pasta e o produto têm o nome definitivo `logoBlocks`.

Configurações anteriormente salvas sob a chave `sponsorsblockplugin` precisam ser migradas para a nova chave para que a ativação, as logos e os títulos sejam reaproveitados. A seleção da barra lateral também precisa usar `logoblocksplugin`. Apenas renomear a pasta não transfere esses dados.

## Validação para desenvolvimento

A partir da raiz da instalação do OJS, execute:

```sh
php plugins/blocks/logoBlocks/tests/image.php
php plugins/blocks/logoBlocks/tests/grid.php
php plugins/blocks/logoBlocks/tests/blocks.php
php plugins/blocks/logoBlocks/tests/gallery.php
php plugins/blocks/logoBlocks/tests/preview.php
```

Os testes cobrem processamento de imagens, prévia, galeria e gerenciamento de logos e blocos. Os testes da grade usam classes reais de formulário e GridHandler do OJS, com requisições e configurações simuladas em memória. Verificam edição, validação de links, POST/CSRF, separação entre revistas, ordenação e persistência ou remoção de arquivos a partir de imagens preparadas. **Não executam upload HTTP nem autenticação no navegador e não substituem a validação do fluxo completo.**

Para verificar o fluxo em uma instalação OJS 3.3, crie um bloco, adicione logos da galeria e por upload, com e sem link, e confira a página pública. Verifique lateral e rodapé, descrições, escala de cinza, ordenação, edição, ocultação e exclusão. Confira também uma segunda revista para verificar a separação das configurações.

## Licença

GNU GPL v3, conforme `docs/COPYING` do OJS.
