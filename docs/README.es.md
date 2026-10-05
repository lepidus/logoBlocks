# Bloques de logos — OJS 3.3

Otros idiomas: [English](../README.md) | [Português (Brasil)](README.pt_BR.md)

El plugin **Bloques de logos** permite mostrar logos de patrocinadores, entidades de apoyo o servicios de indexación en las páginas públicas de la revista, en la **barra lateral**, en una **franja encima del pie de página** o en ambos lugares. Cada logo puede tener un enlace y una descripción breve.

Por ejemplo, la revista puede crear un bloque llamado «Indexadores» con logos y enlaces a sus perfiles en las bases de datos, y otro llamado «Apoyo» con las instituciones que la apoyan. Cada revista configura sus propios bloques, títulos, imágenes y orden de presentación.

![Demostración del plugin Bloques de logos](logoBlocksDemo.gif)

## Funcionalidades

- Permite crear varios bloques, con títulos traducidos a los idiomas de la revista.
- Ofrece una galería de logos incluida en el plugin y permite subir imágenes propias.
- Redimensiona las imágenes a 150 px de ancho, manteniendo la proporción, y ofrece un recorte opcional de márgenes al subir imágenes.
- Permite ordenar, editar, ocultar y eliminar bloques y logos.
- Ofrece opciones por bloque para la ubicación, la visualización de descripciones y el efecto de escala de grises.

**La galería es un catálogo de imágenes, no una integración con los servicios de indexación.** Seleccionar un logo no registra la revista en una base de datos, no confirma su indexación ni busca automáticamente la dirección de su perfil. El equipo de la revista debe elegir los logos e introducir los enlaces correspondientes.

El plugin no envía correos electrónicos, no modifica el flujo de envío o revisión y no crea tareas programadas. **No es necesario configurar cron.** Las imágenes se procesan al preparar la vista previa o guardar un logo; los bloques se muestran cuando OJS renderiza las páginas públicas.

## Requisitos e instalación

Esta versión está destinada a **OJS 3.3**. El servidor necesita:

- PHP con la extensión **GD** y soporte para PNG, JPEG y GIF.
- Un directorio público de la revista en el que OJS pueda escribir.

Para instalar y activar:

1. Coloque la carpeta `logoBlocks`, con los archivos del plugin, en `plugins/blocks/` dentro de la instalación de OJS. El archivo de versión debe estar en `plugins/blocks/logoBlocks/version.xml`.
2. Inicie sesión en OJS como gestor de la revista o administrador y acceda a la revista que desea configurar.
3. En **Ajustes → Sitio web → Módulos → Módulos instalados → Módulos de bloque**, active **Bloques de logos**.
4. En **Ajustes → Sitio web → Apariencia → Configuración → Barra lateral**, añada **Bloques de logos** a la barra lateral y guarde.

**El paso 4 también es necesario para mostrar logos únicamente encima del pie de página.** OJS carga este plugin al procesar la barra lateral. La opción de ubicación de cada bloque determina dónde se mostrará.

Activar el plugin no crea bloques ni añade logos automáticamente. Siga los pasos siguientes para crear el primer bloque.

## Crear el primer bloque

1. En el menú del plugin **Bloques de logos**, abra **Ajustes**.
2. Haga clic en **Añadir bloque**.
3. Rellene el **Título del bloque**, por ejemplo, «Indexadores». Use el selector de idiomas de OJS para rellenar el idioma principal de la revista y, opcionalmente, sus traducciones. El límite es de 200 caracteres por idioma.
4. En **Personalización del bloque**, elija dónde mostrarlo. Por defecto, los bloques nuevos aparecen en la barra lateral, con las descripciones ocultas y las imágenes en color.
5. Haga clic en **Guardar**. En la lista de bloques, haga clic en el lápiz (**Editar**) para abrir el bloque creado y añadir sus logos.
6. Añada al menos un logo mediante una de las opciones siguientes y compruebe el resultado en la página pública de la revista.

El título público corresponde al idioma seleccionado por el visitante; si no hay traducción, utiliza el idioma principal de la revista. Los títulos antiguos, registrados antes de incorporar el soporte de traducciones, se conservan en el idioma principal. **Los bloques sin logos no aparecen en la página pública.**

## Añadir logos

### Elegir un logo de la galería

1. Abra la edición del bloque y haga clic en **Galería de logos**.
2. Seleccione una imagen. Debajo de la galería aparecen los campos de enlace y descripción.
3. Si lo desea, introduzca la dirección completa de la página de la revista en el servicio de indexación, comenzando por `https://` o `http://`, y una descripción de hasta 100 caracteres. Ambos son opcionales.
4. Haga clic en **Guardar**. El logo se añade al final de la lista, con el nombre y la sigla definidos en el catálogo. Después, use **Editar** para ajustar estos datos.

La galería incluye, entre otros, DOAJ, Google Scholar, Latindex, SciELO, Scopus y Web of Science. Las tarjetas muestran la sigla, cuando está disponible, o el nombre, y se ordenan por el nombre completo. La lista de entradas está en [logos/catalog.json](../logos/catalog.json).

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
| **Enlace (opcional)** | Dirección HTTP o HTTPS que se abre en una pestaña nueva al hacer clic en la imagen. Sin enlace, el logo es solo una imagen. |
| **Descripción (opcional)** | Texto simple de hasta 100 caracteres, mostrado debajo de la imagen cuando la visualización de descripciones está activada para ese lugar. No admite formato HTML. |

El título del bloque es multilingüe. Los campos de cada logo tienen un único valor, utilizado en todos los idiomas de la página.

## Personalizar la visualización

Haga clic en el lápiz (**Editar**) del bloque, ajuste las opciones en **Personalización del bloque** y haga clic en **Guardar**.

### Ubicación: barra lateral, pie de página o ambos

En el campo **Seleccione dónde mostrar el bloque**, elija:

| Opción | Resultado en la página pública |
| --- | --- |
| **Mostrar en la barra lateral de la revista** | Logos en una lista en la barra lateral. |
| **Mostrar en el pie de página de la revista** | Logos en una franja horizontal encima del pie de página estándar, sin mostrar ese bloque en la barra lateral. |
| **Mostrar en ambos lugares** | El mismo bloque aparece en la barra lateral y encima del pie de página. |

La franja encima del pie de página distribuye los logos en nuevas líneas cuando falta espacio. Una línea separa el conjunto de bloques del contenido de la revista, sin separadores entre los bloques.

La presentación depende del tema. La franja aparece en las páginas que cargan el plugin a través de la barra lateral y usan el pie de página estándar de OJS 3.3, o un tema que conserve el elemento con la clase `pkp_structure_footer_wrapper`. Mantenga **Bloques de logos** seleccionado en la configuración de la barra lateral incluso al elegir únicamente el pie de página.

### Descripciones debajo de los logos

En **Visualización de la descripción de los logos**, elija **No mostrar**, **Mostrar en la barra lateral de la revista**, **Mostrar en el pie de página de la revista** o **Mostrar en ambos lugares**.

Esta opción controla únicamente el texto debajo de las imágenes; no cambia la ubicación del bloque. Por ejemplo, una descripción configurada solo para el pie de página no aparece si el bloque está configurado solo para la barra lateral. Los logos sin descripción no muestran texto adicional.

Las descripciones están ocultas por defecto. Seleccionar **No mostrar** conserva los textos guardados.

### Escala de grises

Marque **Activar la escala monocromática, los logos se muestran en color al interactuar.** y guarde. Los logos de ese bloque se muestran en escala de grises en la barra lateral y encima del pie de página, recuperando sus colores al pasar el cursor o al enfocar su enlace con el teclado. Al terminar la interacción, vuelven a la escala de grises.

El efecto usa CSS y no modifica los archivos de imagen. Desmarque la opción y guarde para mantener los colores siempre visibles.

## Editar, ordenar y guardar

Para editar un logo, use **Editar** en la fila correspondiente. Puede modificar sus textos y enlace o sustituir la imagen. **Deje el campo de archivo vacío para conservar la imagen actual.** La edición mantiene la posición del logo en la lista; sustituir la imagen elimina el archivo público anterior.

Para reordenar logos o bloques, arrastre el **asa de puntos a la izquierda** de la fila. También puede enfocar el asa con el teclado y usar las flechas arriba y abajo. El orden se guarda automáticamente al soltar el elemento o moverlo con el teclado y se aplica tanto a la barra lateral como a la franja encima del pie de página. Los elementos nuevos se añaden al final de la lista. Si falla el guardado, la pantalla restaura el orden anterior e informa del error.

No todas las acciones dependen del mismo botón **Guardar**:

| Acción | Cuándo se guarda |
| --- | --- |
| Crear o editar el título y la personalización de un bloque | Al hacer clic en **Guardar** en el formulario del bloque. |
| Añadir o editar un logo | Al guardar el formulario de ese logo. |
| Reordenar logos o bloques | Automáticamente, al cambiar el orden. |
| Activar o desactivar un bloque mediante el interruptor | Inmediatamente. |
| Eliminar un logo o un bloque | Después de confirmar la eliminación. |

Cerrar el formulario del bloque sin guardar no deshace las operaciones ya guardadas en los logos, el orden o la visibilidad.

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

El recorte elimina los márgenes exteriores totalmente transparentes o de color uniforme, como blanco o negro, antes de redimensionar. Para fondos opacos, el plugin identifica un color predominante en al menos tres esquinas, con tolerancia a pequeñas variaciones de compresión. Conserva un margen de un píxel de la imagen original y la proporción del contenido restante. En imágenes con transparencia, elimina únicamente los márgenes totalmente transparentes, conservando los dibujos blancos.

El recorte no elimina el fondo interno del logo. Los degradados, las texturas y las esquinas sin color predominante pueden impedir el recorte; los elementos de color muy similar al fondo pueden interpretarse como margen. Las imágenes de un solo color o totalmente transparentes mantienen sus proporciones.

En la galería, las tarjetas ya muestran las imágenes: no hay recorte ni vista previa adicional. La imagen elegida solo se redimensiona. Para recortar un logo ya registrado, abra **Editar** y suba de nuevo la imagen con **Recortar bordes** marcado. Modificar únicamente los textos o el enlace conserva la imagen existente.

### Formatos y límites

- Se aceptan **PNG, JPEG y GIF**, de hasta **5 MB** y **12 millones de píxeles** (ancho × alto). No se acepta SVG.
- También se aplican los límites `upload_max_filesize` y `post_max_size` de PHP. El segundo debe admitir el archivo y los demás campos del formulario.
- Las imágenes se convierten a **PNG**, conservando la transparencia. Los GIF animados se convierten en imágenes estáticas con el primer fotograma.
- El archivo guardado tiene **150 px de ancho** y altura proporcional. Las imágenes más pequeñas también se amplían, lo que puede reducir la nitidez. Se rechazan las imágenes cuya altura final supere los **3000 px**.
- En la página pública, la altura de visualización se limita a **100 px**, con una reducción proporcional del ancho. Por ello, un logo alto puede aparecer con menos de 150 px de ancho. La visualización también se adapta al espacio disponible en la barra lateral.
- Después de un error de validación, seleccione el archivo de nuevo antes de guardar.

## Almacenamiento y separación entre revistas

Las configuraciones se guardan por revista en los ajustes de plugins de OJS; el plugin no crea una tabla propia. Los archivos procesados se almacenan en `public/journals/<id>/`, o en la ruta correspondiente definida por `public_files_dir`.

Cada bloque mantiene su propia lista de logos y opciones. Las operaciones en una revista no modifican los bloques de otras revistas. La única parte compartida es el catálogo de imágenes incluido con los archivos del plugin.

La gestión utiliza el control de acceso de OJS para plugins. Las operaciones de escritura requieren solicitudes POST con un token CSRF.

## Problemas comunes

| Situación | Qué comprobar |
| --- | --- |
| No aparece ningún bloque | Compruebe que el plugin esté activado y seleccionado en la barra lateral, que el interruptor del bloque esté activado y que contenga al menos un logo. |
| El bloque aparece en la barra lateral, pero no encima del pie de página | Compruebe la ubicación seleccionada y guardada en el bloque y si el tema utiliza el elemento `pkp_structure_footer_wrapper`. |
| La descripción no aparece | Rellene la descripción del logo y active su visualización en un lugar donde el bloque también sea visible. |
| No se puede guardar la imagen | Compruebe el formato, el tamaño, la cantidad de píxeles, la extensión GD, los límites de carga de PHP y los permisos de escritura del directorio público. Seleccione el archivo de nuevo después de corregir el problema. |
| El logo no abre una dirección | Edite el logo e introduzca un enlace HTTP o HTTPS. La galería no rellena la dirección automáticamente. |
| El orden no se ha guardado | Vuelva a abrir los ajustes para actualizar la lista e inténtelo de nuevo. |

## Actualización de instalaciones con el nombre antiguo

Estas indicaciones se aplican a quienes ya utilizaban el plugin con el nombre de carpeta antiguo.

La carpeta de instalación es `logoBlocks`, y el campo `<application>` de `version.xml` debe contener `logoBlocks`. Después de renombrar la carpeta de una instalación anterior de `sponsorsBlock` o `sponsors` a `logoBlocks`, abra **Sitio web → Módulos → Módulos instalados** para que OJS registre el producto con la nueva ruta. Después, vuelva a abrir **Sitio web → Apariencia → Configuración → Barra lateral** y compruebe la selección del plugin.

La clase es `LogoBlocksPlugin`, y la clave interna es `logoblocksplugin`. Esta clave corresponde al nombre de la clase en minúsculas, como exige la búsqueda de plugins activados en OJS 3.3. La carpeta y el producto tienen el nombre definitivo `logoBlocks`.

Las configuraciones guardadas anteriormente bajo la clave `sponsorsblockplugin` deben migrarse a la nueva clave para conservar la activación, los logos y los títulos. La selección de la barra lateral también debe usar `logoblocksplugin`. Renombrar únicamente la carpeta no transfiere estos datos.

## Validación para desarrollo

Desde la raíz de la instalación de OJS, ejecute:

```sh
php plugins/blocks/logoBlocks/tests/image.php
php plugins/blocks/logoBlocks/tests/grid.php
php plugins/blocks/logoBlocks/tests/blocks.php
php plugins/blocks/logoBlocks/tests/gallery.php
php plugins/blocks/logoBlocks/tests/preview.php
```

Las pruebas cubren el procesamiento de imágenes, la vista previa, la galería y la gestión de logos y bloques. Las pruebas de la cuadrícula usan clases reales de formulario y GridHandler de OJS, con solicitudes y configuraciones simuladas en memoria. Verifican la edición, la validación de enlaces, POST/CSRF, la separación entre revistas, el orden y la persistencia o eliminación de archivos a partir de imágenes preparadas. **No realizan cargas HTTP ni autenticación en el navegador y no sustituyen la validación del flujo completo.**

Para comprobar el flujo en una instalación de OJS 3.3, cree un bloque, añada logos desde la galería y mediante carga, con y sin enlace, y compruebe la página pública. Revise la barra lateral y el pie de página, las descripciones, la escala de grises, el orden, la edición, la ocultación y la eliminación. Compruebe también una segunda revista para verificar la separación de las configuraciones.

## Licencia

GNU GPL v3, según `docs/COPYING` de OJS.
