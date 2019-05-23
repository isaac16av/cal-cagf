# Calendario CAGF

## ¿En qué consiste?
Es un proyecto para cumplir el _Trabajo Comunal Universitario_.
Se trata de una página web, con información sobre la Casa de la Cultura de Heredia, y lo principal, un módulo de Drupal y un calendario para que la comunidad pueda estar informada de las actividades que se realizan en la Casa AGF.

## Pre-Requisitos 📋
- Servidor PHP.
- Sitio de Drupal instalado en dicho servidor.

## 1. Modúlo para Drupal ⚙️
El cual permite el mantenimiento para las actividades que solicite el calendario.
Y el API al cual el calendario hará la solicitud.

### 1.1. Instalación del modulo
- Se debe descomprimir el modulo en la carpeta **modules** de Drupal.
- Luego se procede a la sección de **Ampliar** en Drupal.
- Seleccionar **Actividades CAGF**.
- Por último click en **instalar**.

### 1.2. Hacer GET al API
Para acceder al API, se deberá hacer mediante:
```
http://www.sitio.com/api/cal/actividades?start='fecha_s'&end='fecha_e'
```
Donde los parámetros son:
- Fecha_s: Es la limite de inicio
- Fecha_e: Es la limite de final

Y el API retornará las actividades que se encuentren entre ese rango.

## 2. Sitio en HTML y JavaScript 💻
Este sitio, consiste en un conjunto de HTML's, los cuales muestran información de la Casa AGF, el calendario y el formulario de Google Forms embeido.

### 2.1. Ubicación
Los archivos se encuentran actualmente en la carpeta: **modules > mancal_cagf > app**.

Pero para mejor comprensión y mejor estética de la ruta, se recomienda mover la carpeta **app** a la _ruta raíz de drupal_, de manera que para acceder al sitio sería:

```
http://www.sitio.com/app/cagf
```

## 3. Autores 👨‍💻
* **Isaac Alfaro** | [isaac16av](https://github.com/isaac16av)
* **Fabián Aguilar** | [Faac97](https://github.com/faac97)

## 4. Desarrollado con 🛠️
* [Bootstrap](https://getbootstrap.com/) - Como framework css
* [FullCalendar](https://fullcalendar.io/) - Para darle vida al calendario
* [Drupal](https://www.drupal.org/) - Para desarrollar el módulo
