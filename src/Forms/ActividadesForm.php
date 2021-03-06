<?php

namespace Drupal\mancal_cagf\Forms;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Url;
use Drupal\mancal_cagf\Repository\ActividadesRepo;
use Drupal\mancal_cagf\Repository\CategoriasRepo;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ActividadesForm extends FormBase
{

    public function getFormId()
    {
        return 'actividades_agregar';
    }

    public function buildForm(array $form, FormStateInterface $form_state, $actividad = NULL, $tipo = NULL)
    {
        //Si no hay actividad
        if ($actividad) {
            if ($actividad == 'invalid') {
                drupal_set_message(t('Actividad errónea'), 'error');
                return new RedirectResponse(Drupal::url('mancal_cagf.listarActividades'));
            }
            $form['id_actividad'] = [
                '#type' => 'hidden',
                '#value' => $actividad->id_actividad,
            ];
        }

        //Trae las categorias
        $categorias_lista = CategoriasRepo::getCategorias();
        if (!$categorias_lista) {
            drupal_set_message(t('Error, no se encuentran categorias'), 'error');
            return new RedirectResponse(Drupal::url('mancal_cagf.listarActividades'));
        }
        //Recorre las categorias para ponerlas en un array con las ociones
        $categorias_select = ['' => 'Seleccionar'];
        foreach ($categorias_lista as $cat) {
            $categorias_select[$cat['id_cat']] = $cat['nombre'];
        }

        $form['#attributes']['novalidate'] = '';

        //Valida si hay un objeto actividad, para saber si se esta editando o no
        //Si se edita, que sea posible de cancelar
        if ($actividad) {
            $form['cancelacion'] = [
                '#type' => 'details',
                "#title" => "Cancelación",
                '#open' => TRUE,
            ];

            $form['cancelacion']['cancelado'] = [
                '#type' => 'checkbox',
                '#title' => t('Cancelado'),
                '#default_value' => ($actividad) ? $actividad->cancelado : 0,
            ];

            $form['cancelacion']['motivo_cancelacion'] = [
                '#type' => 'textarea',
                '#title' => t('Motivo de Cancelación'),
                '#default_value' => ($actividad) ? $actividad->motivo_cancelacion : 0,
                '#maxlength' => 500,
            ];
        }

        $form['general'] = [
            '#type' => 'details',
            "#title" => "Detalles generales",
            '#open' => TRUE,
        ];

        $form['general']['titulo'] = [
            '#type' => 'textfield',
            '#title' => t('Titulo'),
            '#required' => TRUE,
            '#maxlength' => 150,
            '#default_value' => ($actividad) ? $actividad->titulo : '',
        ];

        $form['general']['descripcion'] = [
            '#type' => 'textarea',
            '#title' => t('Descripción'),
            '#required' => TRUE,
            '#maxlength' => 1000,
            '#default_value' => ($actividad) ? $actividad->descripcion : '',
        ];

        $form['general']['categoria'] = [
            '#type' => 'select',
            '#title' => t('Categoria'),
            '#options' => $categorias_select,
            '#required' => TRUE,
            '#default_value' => ($actividad) ? $actividad->categoria : '',
        ];

        $form['contacto'] = [
            '#type' => 'details',
            "#title" => "Información de Contacto",
            '#open' => TRUE,
        ];

        $form['contacto']['encargado'] = [
            '#type' => 'textfield',
            '#title' => t('Persona a Cargo'),
            '#maxlength' => 300,
            '#default_value' => ($actividad) ? $actividad->encargado : '',
        ];

        $form['contacto']['contacto'] = [
            '#type' => 'textfield',
            '#title' => t('Contacto'),
            '#maxlength' => 400,
            '#default_value' => ($actividad) ? $actividad->contacto : '',
        ];

        $form['contacto']['link_publicacion_fb'] = [
            '#type' => 'textfield',
            '#title' => t('Enlace a publicación de Facebook'),
            '#maxlength' => 300,
            '#default_value' => ($actividad) ? $actividad->link_publicacion_fb : '',
        ];

        $form['fechas'] = [
            '#type' => 'details',
            "#title" => "Fechas",
            '#open' => TRUE,
        ];

        $form['fechas']['inicio_fecha'] = [
            '#type' => 'date',
            '#title' => t('Fecha de Inicio'),
            '#required' => TRUE,
            '#default_value' => ($actividad) ? $actividad->inicio_fecha : '',
        ];

        if ($tipo == 2 || $tipo == 4 || $actividad->hora) {
            $form['fechas']['hora'] = [
                '#type' => 'textfield',
                '#title' => t('Hora'),
                '#required' => TRUE,
                '#default_value' => ($actividad) ? substr($actividad->hora, 0, 5) : '',
            ];
        }

        if ($tipo == 3 || $tipo == 4 || $actividad->final_fecha) {
            $form['fechas']['final_fecha'] = [
                '#type' => 'date',
                '#title' => t('Fecha Final'),
                '#required' => TRUE,
                '#default_value' => ($actividad) ? $actividad->final_fecha : '',
            ];
        }

        if ($tipo == 4 || $actividad->frecuencia_dias) {
            $form['fechas']['frecuencia_dias'] = [
                '#type' => 'select',
                '#title' => t('Dia específico en que se repetirá'),
                '#options' => [
                    '' => 'Seleccionar',
                    1 => 'Domingo',
                    2 => 'Lunes',
                    3 => 'Martes',
                    4 => 'Miércoles',
                    5 => 'Jueves',
                    6 => 'Viernes',
                    7 => 'Sábado',
                ],
                '#required' => TRUE,
                '#default_value' => ($actividad) ? $actividad->frecuencia_dias : '',
            ];
        }

        $form['actions'] = ['#type' => 'actions'];
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => 'Guardar',
        ];

        $form['actions']['cancel'] = [
            '#type' => 'link',
            '#title' => 'Cancelar',
            '#attributes' => ['class' => ['button', 'button--primary']],
            '#url' => Url::fromRoute('mancal_cagf.listarActividades'),
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);

        //Validacion de hora
        $hora = $form_state->getValue('hora');
        if (!empty($hora)) {
            if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $hora)) { //Si tiene el formato de hora
                $form_state->setErrorByName('hora', $this->t('La hora brindada no está en el formato solicitado. "00:00".'));
            }
        }

        $fecha_final = $form_state->getValue('final_fecha'); //Toma la fecha fina
        //Si la fecha final es menor que la inicial
        if (date("d/m/Y", strtotime($fecha_final)) != date("d/m/Y", strtotime('31/12/1969'))) {
            $fecha_inicial = $form_state->getValue('inicio_fecha');

            //Conversion a tipo fecha
            $f_inicio = new \DateTime(date("Y-m-d", strtotime($fecha_inicial)));
            $f_final = new \DateTime(date("Y-m-d", strtotime($fecha_final)));

            //Comparacion
            if ($f_final < $f_inicio) {
                $form_state->setErrorByName('final_fecha', $this->t('La fecha final debe ser después de la inicial.'));
            }
        }
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        //Toma los datos introducidos
        $id = $form_state->getValue('id_actividad');
        $fecha_final = $form_state->getValue('final_fecha');
        $frecuencia_dias = $form_state->getValue('frecuencia_dias');
        $hora = $form_state->getValue('hora');
        $cancelado = $form_state->getValue('cancelado');
        $motivo_cancelacion = $form_state->getValue('motivo_cancelacion');

        //Los pone en el array fields
        $fields = [
            'titulo' => SafeMarkup::checkPlain($form_state->getValue('titulo')),
            'descripcion' => $form_state->getValue('descripcion'),
            'categoria' => $form_state->getValue('categoria'),
            'encargado' => $form_state->getValue('encargado'),
            'inicio_fecha' => $form_state->getValue('inicio_fecha'),
            'contacto' => $form_state->getValue('contacto'),
            'link_publicacion_fb' => $form_state->getValue('link_publicacion_fb'),
        ];

        //valida que haya fecha final
        if (date("d/m/Y", strtotime($fecha_final)) != date("d/m/Y", strtotime('31/12/1969'))) {
            $fields['final_fecha'] = $fecha_final;
        } else {
            $fields['final_fecha'] = NULL;
        }

        //Valida que haya hora
        if (!empty($hora)) {
            $fields['hora'] = $hora;
        } else {
            $fields['hora'] = NULL;
        }

        //Valida que haya frecuencia
        if (!empty($frecuencia_dias)) {
            $fields['frecuencia_dias'] = $frecuencia_dias;
        } else {
            $fields['frecuencia_dias'] = NULL;
        }

        //Si se esta editando
        if (!empty($id) && ActividadesRepo::existe($id)) {
            $fields['cancelado'] = $cancelado;
            $fields['motivo_cancelacion'] = $motivo_cancelacion;
            //Actualiza en bd
            ActividadesRepo::actualizar($id, $fields);
            $message = 'Actividad ' . $fields['titulo'] . ' actualizada';
        } else { //Si se esta creando una nueva
            //Guarda en bd
            ActividadesRepo::agregar($fields);
            $message = 'Nueva actividad ' . $fields['titulo'] . ' guardada';
        }

        drupal_set_message($message);
        $form_state->setRedirect('mancal_cagf.listarActividades');
    }
}
