[![GitHub release](https://img.shields.io/github/v/release/lepidus/logoBlocks)](https://github.com/lepidus/logoBlocks/releases)
[![License](https://img.shields.io/badge/license-GPL--3.0-orange)](https://www.gnu.org/licenses/gpl-3.0.html)
[![Number of downloads](https://img.shields.io/github/downloads/lepidus/logoBlocks/total?color=brightgreen)](https://github.com/lepidus/logoBlocks/releases)

[English](../README.md) | [Português Brasileiro](README.pt_BR.md) | **Español**

# Bloques de logos

El plugin **Bloques de logos** permite mostrar logos en las páginas públicas de la revista, en la **barra lateral**, en una **franja encima del pie de página** o en ambos lugares. Cada logo puede tener un enlace y una descripción breve.

Por ejemplo, la revista puede crear un bloque llamado «Indexadores» con logos y enlaces a sus perfiles en las bases de datos, y otro llamado «Apoyo» con las instituciones que la apoyan. Cada revista configura sus propios bloques, títulos, imágenes y orden de presentación.

![Demostración del plugin Bloques de logos](logoBlocksDemo.gif)

## Funcionalidades

- Permite crear varios bloques, con títulos traducidos a los idiomas de la revista.
- Ofrece una galería de logos incluida en el plugin y permite subir imágenes propias.
- Permite ordenar, editar, ocultar y eliminar bloques y logos.
- Ofrece opciones de personalización por bloque para la visualización de descripciones y el efecto de escala de grises.

**La galería es un catálogo de imágenes, no una integración con los servicios de indexación.** Seleccionar un logo no registra la revista en una base de datos, no confirma su indexación ni busca automáticamente la dirección de su perfil. El equipo de la revista debe elegir los logos e introducir los enlaces correspondientes.

Los bloques y sus respectivos logos se muestran en las páginas públicas de OJS.

## Compatibilidad

Este plugin es compatible con **OJS 3.3**.

## Crear el primer bloque

1. En el menú del plugin **Bloques de logos**, abra **Ajustes**.
2. Haga clic en **Añadir bloque**.
3. Rellene el **Título del bloque**, por ejemplo, «Indexadores». Use el selector de idiomas de OJS para rellenar el idioma principal de la revista y, opcionalmente, sus traducciones. El límite es de 200 caracteres por idioma.
4. En **Personalización del bloque**, elija dónde mostrarlo. Por defecto, los bloques nuevos aparecen en la barra lateral, con las descripciones ocultas y las imágenes en color.
5. Haga clic en **Guardar**. En la lista de bloques, haga clic en el lápiz (**Editar**) para abrir el bloque creado y añadir sus logos.
6. Añada al menos un logo y compruebe el resultado en la página pública de la revista.

El título público corresponde al idioma seleccionado por el visitante; si no hay traducción, utiliza el idioma principal de la revista. **Los bloques sin logos no aparecen en la página pública.**

## Añadir logos

### Elegir un logo de la galería

1. Abra la edición del bloque y haga clic en **Galería de logos**.
2. Seleccione una imagen. Debajo de la galería aparecen los campos de enlace y descripción. Ambos son opcionales.
3. Haga clic en **Guardar**. El logo se añade al final de la lista, con el nombre y la sigla definidos en el catálogo. Después, use **Editar** para ajustar estos datos.

El catálogo y sus imágenes se incluyen con el plugin; no se descargan imágenes de servicios externos durante el uso. Todos los bloques comparten este catálogo, pero cada incorporación guarda una copia procesada en el directorio público de la revista. Editar o eliminar un logo registrado no modifica el catálogo ni las copias de otros bloques o revistas.

### Subir una imagen propia

1. Abra la edición del bloque y haga clic en **Añadir logo**.
2. Rellene **Título del logo** y seleccione la imagen PNG, JPEG o GIF.
3. Rellene, si lo desea, la sigla, el enlace y la descripción.
4. Compruebe la vista previa. Si necesita eliminar márgenes vacíos, marque **Recortar bordes**.
5. Guarde el formulario del logo.

Las imágenes subidas se añaden únicamente al bloque que se está editando; no pasan a formar parte de la galería compartida.

### Para qué sirve cada campo

| Campo | Uso |
| --- | --- |
| **Título del logo** | Nombre obligatorio, de hasta 200 caracteres. Se usa como texto alternativo de la imagen y como información al pasar el cursor. |
| **Sigla (opcional)** | Hasta 30 caracteres. Aparece destacada en la lista administrativa, junto al nombre completo; no sustituye el texto alternativo de la imagen. |
| **Enlace (opcional)** | Dirección que se abre en una pestaña nueva al hacer clic en la imagen. Sin enlace, el logo es solo una imagen. |
| **Descripción (opcional)** | Texto simple de hasta 100 caracteres, mostrado debajo de la imagen cuando la visualización de descripciones está activada para ese lugar. No admite formato HTML. |

El título del bloque es multilingüe. Los campos de cada logo tienen un único valor, utilizado en todos los idiomas de la página.

## Personalizar la visualización

Haga clic en el lápiz (**Editar**) del bloque, ajuste las opciones en **Personalización del bloque** y haga clic en **Guardar**.

| Opción | Resultado en la página pública |
| --- | --- |
| **Ubicación** | Los bloques pueden mostrarse en la barra lateral, en el pie de página o en ambos lugares. La presentación depende del tema. Mantenga **Bloques de logos** seleccionado en la configuración de la barra lateral incluso al elegir únicamente el pie de página. |
| **Descripciones debajo de los logos** | En **Visualización de la descripción de los logos**, elija **No mostrar**, **Mostrar en la barra lateral de la revista**, **Mostrar en el pie de página de la revista** o **Mostrar en ambos lugares**. Las descripciones están ocultas por defecto. Seleccionar No mostrar conserva los textos guardados. |
| **Escala de grises** | Marque **Activar la escala monocromática** y guarde. Los logos de ese bloque se muestran en escala de grises en la barra lateral y encima del pie de página, recuperando sus colores al pasar el cursor o al enfocar su enlace con el teclado. |

## Editar, ordenar y guardar

Para editar un logo, use **Editar** en la fila correspondiente. Puede modificar sus textos y enlace o sustituir la imagen. **Deje el campo de archivo vacío para conservar la imagen actual.**

Para reordenar logos o bloques, arrastre el **asa de puntos a la izquierda** de la fila. También puede enfocar el asa con el teclado y usar las flechas arriba y abajo. El orden se guarda automáticamente al soltar el elemento o moverlo con el teclado y se aplica tanto a la barra lateral como a la franja encima del pie de página. Los elementos nuevos se añaden al final de la lista. Si falla el guardado, la pantalla restaura el orden anterior e informa del error.

## Ocultar, desactivar o eliminar: ¿qué ocurre?

| Acción | Efecto | ¿Se conservan los datos? |
| --- | --- | --- |
| Desactivar el interruptor de un bloque | Oculta ese bloque en la barra lateral y encima del pie de página. Los demás mantienen sus configuraciones. | Sí: logos, textos, orden y opciones. |
| Desactivar el plugin en **Módulos instalados** | Oculta todos los bloques del plugin en esa revista. No modifica los envíos, las revisiones ni los correos de OJS. | Sí: las configuraciones y los archivos de los logos permanecen almacenados. |
| Retirar **Bloques de logos** de la configuración de la barra lateral | Impide su carga a través de la barra lateral, afectando también a la franja encima del pie de página. | Sí. |
| Eliminar un logo | Elimina el logo del bloque y su archivo público. | No para el logo eliminado. |
| Eliminar un bloque mediante la papelera | Elimina el bloque, sus configuraciones, todos sus logos y los archivos públicos correspondientes. | No para el bloque eliminado. |

El interruptor junto al lápiz está **verde cuando el bloque está activado** y **gris cuando está desactivado**. Los bloques nuevos comienzan activados. Para volver a mostrar un bloque oculto, active de nuevo su interruptor; si desactivó el plugin, actívelo de nuevo y compruebe su selección en la barra lateral.

**Ocultar un bloque o desactivar el plugin no convierte sus archivos en privados.** Las imágenes permanecen en el directorio público y puede acceder a ellas cualquier persona que tenga sus direcciones. Para eliminar un logo y su archivo mediante el plugin, use la eliminación.

## Recorte, vista previa y tamaño de las imágenes

Al subir o sustituir una imagen, la vista previa muestra cómo se guardará. **Recortar bordes** está desmarcado inicialmente: márquelo para comprobar el recorte o desmárquelo para volver a la versión sin recorte. Generar la vista previa no guarda el logo; es necesario confirmar el formulario.

El recorte elimina los márgenes exteriores totalmente transparentes o de color uniforme, como blanco o negro, antes de redimensionar.

## Almacenamiento y separación entre revistas

Cada bloque mantiene su propia lista de logos y opciones. Las operaciones en una revista no modifican los bloques de otras revistas. La única parte compartida es el catálogo de imágenes incluido con los archivos del plugin.

## Licencia

Este plugin está licenciado bajo la GNU General Public License v3.0

Copyright (c) 2013-2026 Lepidus Tecnologia
