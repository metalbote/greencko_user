<?php

namespace Drupal\greencko_user\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RequestContext;
use Symfony\Component\DependencyInjection\ContainerInterface;


class GreenckoUserSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected $requestContext;

  /**
   * Constructs a GreenckoUserSettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   *
   * @param \Drupal\Core\Routing\RequestContext $request_context
   *   The request context.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RequestContext $request_context) {
    parent::__construct($config_factory);

    $this->requestContext = $request_context;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('router.request_context')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'greencko_user_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['greencko_user.settings'];
  }


  /**
   * {@inheritdoc}
   */

  public function buildForm(array $form, FormStateInterface $form_state) {

    // Form constructor.
    $form = parent::buildForm($form, $form_state);

    // Default settings.
    $config = $this->config('greencko_user.settings');

    $form['login'] = [
      '#type' => 'details',
      '#title' => $this->t('Login'),
      '#open' => TRUE,
    ];
    $form['login']['login_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Define login path '),
      '#default_value' => $config->get('login.login_path'),
      '#size' => 40,
      '#field_prefix' => $this->requestContext->getCompleteBaseUrl(),
    ];

    $form['user'] = [
      '#type' => 'details',
      '#title' => $this->t('User'),
      '#open' => TRUE,
    ];
    $form['user']['user_profile'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use administration theme for user profile'),
      '#default_value' => $config->get('user.user_profile'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('greencko_user.settings')
      ->set('login.login_path', $form_state->getValue('login_path'))
      ->set('user.user_profile', $form_state->getValue('user_profile'))
      ->save();

    parent::submitForm($form, $form_state);
    drupal_flush_all_caches();
  }
}
