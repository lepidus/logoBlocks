[![Release](https://img.shields.io/badge/release-v2.3.0.0-blue)](../version.xml) [![License](https://img.shields.io/badge/license-GPL--3.0-orange)](https://www.gnu.org/licenses/gpl-3.0.html)

[English](../README.md) | **Português Brasileiro** | [Español](README.es.md)

# Blocos de logos

O plugin **Blocos de logos** permite apresentar logos nas páginas públicas da revista, na **barra lateral**, em uma **faixa acima do rodapé** ou nos dois lugares. Cada logo pode ter um link e uma descrição curta.

Por exemplo, a revista pode criar um bloco chamado “Indexadores” com logos e links para seus perfis nas bases, e outro chamado “Apoio” com as instituições que a apoiam. Cada revista configura seus próprios blocos, títulos, imagens e ordem de apresentação.

![Demonstração do plugin Blocos de logos](logoBlocksDemo.gif)

## O que o plugin faz

- Permite criar vários blocos, com títulos traduzidos para os idiomas da revista.
- Oferece uma galeria de logos incluída no plugin e permite enviar imagens próprias.
- Permite ordenar, editar, ocultar e excluir blocos e logos.
- Oferece opções de personalização por bloco para exibição das descrições e efeito de escala de cinza.

**A galeria é um catálogo de imagens, não uma integração com os indexadores.** Selecionar uma logo não cadastra a revista em uma base, não confirma sua indexação e não busca automaticamente o endereço de seu perfil. Cabe à equipe da revista escolher as logos e informar os links correspondentes.

Os blocos e seus respectivos logos são exibidos nas páginas públicas do OJS.

## Compatibilidade

Este plugin é compatível com o **OJS 3.3**.


## Criar o primeiro bloco

1. No menu do plugin **Blocos de logos**, abra **Configurações**.
2. Clique em **Adicionar bloco**.
3. Preencha o **Título do bloco**, por exemplo, “Indexadores”. Use o seletor de idiomas do OJS para preencher o idioma principal da revista e, opcionalmente, suas traduções. O limite é de 200 caracteres por idioma.
4. Em **Personalização do bloco**, escolha onde exibi-lo. Por padrão, novos blocos aparecem na lateral, com descrições ocultas e imagens coloridas.
5. Clique em **Salvar**. Na lista de blocos, clique no lápis (**Editar**) para abrir o bloco criado e adicionar suas logos.
6. Inclua ao menos uma logo e confira o resultado na página pública da revista.

O título público acompanha o idioma selecionado pelo visitante; se não houver tradução, usa o idioma principal da revista. **Blocos sem logos não aparecem na página pública.**

## Adicionar logos

### Escolher uma logo da galeria

1. Abra a edição do bloco e clique em **Galeria de logos**.
2. Selecione uma imagem. Abaixo da galeria, aparecem os campos de link e descrição. Ambos são opcionais.
3. Clique em **Salvar**. A logo entra no final da lista, com o nome e a sigla definidos pelo catálogo. Depois, use **Editar** para ajustar esses dados.

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
| **Link (opcional)** | Endereço aberto em uma nova aba ao clicar na imagem. Sem link, a logo é apenas uma imagem. |
| **Descrição (opcional)** | Texto simples de até 100 caracteres, exibido abaixo da imagem quando a exibição de descrições estiver habilitada para aquele local. Não aceita formatação HTML. |

O título do bloco é multilíngue. Os campos de cada logo têm um único valor, usado em todos os idiomas da página.

## Personalizar a exibição

Abra o lápis (**Editar**) do bloco, ajuste as opções em **Personalização do bloco** e clique em **Salvar**.

| Opção | Resultado na página pública |
| --- | --- |
| **Localização** | Os blocos podem ser exibidos na lateral, rodapé ou ambos. A apresentação depende do tema. Mantenha **Blocos de logos** selecionado na configuração da barra lateral mesmo ao escolher apenas o rodapé. |
| **Descrições abaixo das logos** | Em **Exibição da descrição dos logos**, escolha **Não exibir**, **Exibir na lateral da revista**, **Exibir no rodapé da revista** ou **Exibir em ambos os lugares**. As descrições ficam ocultas por padrão. Selecionar Não exibir preserva os textos cadastrados.|
| **Escala de cinza** | Marque **Ativar escala monocromática** e salve. As logos daquele bloco ficam em escala de cinza na lateral e acima do rodapé, recuperando as cores ao passar o mouse ou focar seu link pelo teclado. |

## Editar, ordenar e salvar

Para editar uma logo, use **Editar** na linha correspondente. Você pode alterar seus textos e link ou substituir a imagem. **Deixe o campo de arquivo vazio para preservar a imagem atual.**

Para reordenar logos ou blocos, arraste a **alça de pontos à esquerda** da linha. Também é possível focar a alça pelo teclado e usar as setas para cima e para baixo. A ordem é salva automaticamente ao soltar ou mover pelo teclado e vale tanto para a lateral quanto para a faixa acima do rodapé. Novos itens entram no final da lista. Em caso de falha na gravação, a tela restaura a ordem anterior e informa o erro.


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

O recorte remove margens externas totalmente transparentes ou de cor uniforme, como branco ou preto, antes do redimensionamento.


## Armazenamento e separação entre revistas

Cada bloco mantém sua própria lista de logos e opções. As operações em uma revista não alteram os blocos de outras revistas. A única parte compartilhada é o catálogo de imagens que acompanha os arquivos do plugin.


## Licença

Este plugin é licenciado sob a GNU General Public License v3.0

Copyright (c) 2013-2026 Lepidus Tecnologia
