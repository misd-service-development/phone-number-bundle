<?php if ($widget == Misd\PhoneNumberBundle\Form\Type\PhoneNumberType::WIDGET_COUNTRY_CHOICE): ?>
    <div <?php echo $view['form']->block($form, 'widget_container_attributes') ?>>
        <?php echo $view['form']->widget($form['country']).$view['form']->widget($form['number']); ?>
    </div>
<?php else: ?>
    <?php echo $view['form']->block($form, 'form_widget_simple'); ?>
<?php endif ?>
