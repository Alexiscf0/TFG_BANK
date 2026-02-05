# 🏔️ Kibo: Ahorro Inteligente

> **Más que una app de gastos, tu copiloto para cumplir metas.**

---

## 📖 Sobre el Proyecto

Muchas personas revisan su cuenta bancaria y piensan: *"Tengo dinero"*. Pero olvidan que mañana llega el cobro del alquiler o el seguro del coche. Esa falsa sensación de riqueza es el enemigo número uno del ahorro.

**Kibo nace para solucionar ese problema.**

No es una simple calculadora. Es un sistema que **traduce tus finanzas a un lenguaje humano**, diciéndote no cuánto dinero tienes en total, sino cuánto puedes gastar realmente sin poner en peligro tus obligaciones ni tus sueños.

---

## 🌟 ¿Qué hace a Kibo diferente?

La aplicación se basa en tres pilares fundamentales diseñados para reducir la ansiedad financiera:

### 1. El "Saldo Disponible Real"
Kibo cambia las reglas del juego. En la pantalla principal, no verás el saldo total de tu banco.
* **La Magia:** Kibo toma tu dinero total y le resta automáticamente tus gastos fijos previstos y tus ahorros del mes.
* **El Resultado:** Ves solo el dinero que está **libre de culpa** para gastar en un café o una cena.

### 2. Metas antes que Gastos
La mayoría de apps te muestran primero en qué has gastado (dolor). Kibo te muestra cuánto te falta para tu viaje o tu fondo de emergencia (motivación).
* **Visualización:** Barras de progreso claras que te animan a "pagarte a ti mismo" primero.

### 3. Feedback Emocional
Tu app no debería ser fría. Kibo te saluda y te da un diagnóstico rápido:
* *"Vas por buen camino, ¡sigue así!"* 🚀
* *"Cuidado, el ritmo de gastos es alto esta semana"* ⚠️

### 4. El Radar de "Gastos Hormiga"

* A menudo, no es el alquiler ni las compras grandes lo que descuadra el presupuesto, sino esas pequeñas monedas que "no se notan". El café diario, la suscripción que no usas o el snack de media mañana.
* Kibo cuenta con un sistema específico para detectar estas micro-fugas de dinero:
* **Visibilidad Agregada:** Kibo no te regaña por comprar un café de 2€. Pero sí te muestra que ese hábito representa 60€ al mes o 720€ al año.
* **Detección de Patrones:** La app identifica gastos repetitivos de bajo importe y los agrupa bajo una lupa especial, permitiéndote decidir si ese gasto realmente te aporta valor o si es solo inercia.
* **Conversión a Metas:** Te propone el reto: "¿Y si cambias 2 cafés a la semana por tu viaje a Japón?". Convierte el gasto inconsciente en ahorro consciente.

---

## 📱 Recorrido por la App

### 🏠 El Inicio (Dashboard)
Un panel de control diseñado para ser leído en 3 segundos. Sin listas infinitas, solo lo importante: tu saldo real, el progreso de tus objetivos y los últimos 3 movimientos clave.

### 📊 Análisis Inteligente
Gráficos sencillos que responden preguntas concretas: *¿En qué se me está yendo el dinero este mes?* y *¿Estoy ahorrando más que el mes pasado?*

### 🎯 Gestión de Metas
Crea "huchas virtuales" para organizar tu dinero. Ya sea para un coche nuevo o para las vacaciones, cada euro tiene un nombre y un apellido.

---

## ⚙️ Manual de Puesta en Marcha (Técnico)

> 🛑 **REQUISITO CRÍTICO:** Siga estos pasos en orden exacto. Si la configuración del servidor no es correcta, la aplicación no conectará con la base de datos.

### 1. Preparación del Entorno (XAMPP)
Es necesario tener **XAMPP** instalado y el servidor Apache detenido antes de configurar.

1.  Abra el panel de control de XAMPP.
2.  Haga clic en **Config** (junto a Apache) > **PHP (php.ini)**.
3.  Se abrirá un archivo de texto. Busque y **descomente** (quite el punto y coma `;` inicial) las siguientes líneas:
    ```ini
    extension=zip
    extension=mongodb
    ```
    *(Nota: Si no encuentra `extension=mongodb`, deberá descargar la DLL del driver de MongoDB para PHP y añadirla a la carpeta `xampp/php/ext`, o asegurarse de que su versión de XAMPP la incluye).*
4.  Guarde el archivo y **Inicie (Start)** el servidor Apache.

### 2. Despliegue del Código
1.  Descargue o clone este repositorio.
2.  Mueva la carpeta del proyecto dentro del directorio público de XAMPP:
    * Ruta típica: `C:\xampp\htdocs\`
    * El resultado debe ser: `C:\xampp\htdocs\Kibo`

### 3. Instalación de Dependencias
Es necesario instalar la librería de conexión a base de datos mediante **Composer**.

1.  Abra una terminal dentro de la carpeta del proyecto (`C:\xampp\htdocs\Kibo`).
2.  Ejecute el siguiente comando para descargar el driver de MongoDB:
    ```bash
    composer require mongodb/mongodb
    ```

### 4. Ejecución
Abra su navegador web favorito e introduzca la siguiente URL para iniciar la aplicación:

👉 **[http://localhost/Kibo/Pages/login.html](http://localhost/Kibo/Pages/login.html)**

---
