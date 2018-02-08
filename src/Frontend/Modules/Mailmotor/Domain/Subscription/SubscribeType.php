<?php

namespace Frontend\Modules\Mailmotor\Domain\Subscription;

use App\Service\Module\ModuleSettings;
use DateTime;
use Frontend\Core\Engine\Navigation;
use App\Component\Locale\FrontendLocale;
use Frontend\Modules\Mailmotor\Domain\Subscription\Command\Subscription;
use MailMotor\Bundle\MailMotorBundle\Exception\NotImplementedException;
use MailMotor\Bundle\MailMotorBundle\Helper\Subscriber;
use App\Component\Locale\Language;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscribeType extends AbstractType
{
    /**
     * @var array
     */
    protected $interests;

    /**
     * @var ModuleSettings
     */
    protected $moduleSettings;

    /**
     * @var Subscriber
     */
    protected $subscriber;

    public function __construct(Subscriber $subscriber, ModuleSettings $moduleSettings)
    {
        $this->subscriber = $subscriber;
        $this->moduleSettings = $moduleSettings;
        $this->interests = $this->getInterests();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //-- Set the default submit action, this is for the widget to work properly.
        $builder->setAction(Navigation::getUrlForBlock('Mailmotor', 'Subscribe'));

        $builder->add(
            'email',
            EmailType::class,
            [
                'required' => true,
                'label' => 'lbl.Email',
                'attr' => [
                    'placeholder' => \SpoonFilter::ucfirst(Language::lbl('YourEmail')),
                ],
            ]
        );

        if (!empty($this->interests)) {
            $builder->add(
                'interests',
                ChoiceType::class,
                [
                    'choices' => $this->interests,
                    'expanded' => true,
                    'multiple' => true,
                ]
            );
        }

        $builder->add(
            'subscribe',
            SubmitType::class,
            [
                'label' => \SpoonFilter::ucfirst(Language::lbl('Subscribe')),
            ]
        );
    }

    public function getInterests(): array
    {
        $interests = [];

        // should we be checking interests (CampaignMonitor for example has no interests)
        $mailMotorInterestsCheckInterests = $this->moduleSettings->get('Mailmotor', 'check_interests', true);
        if (!$mailMotorInterestsCheckInterests) {
            return [];
        }

        try {
            $mailMotorInterests = $this->moduleSettings->get('Mailmotor', 'interests');
            $mailMotorInterestsLastChecked = $this->moduleSettings->get('Mailmotor', 'interests_last_checked');

            // get cached interests
            if (is_array($mailMotorInterests)
                && $mailMotorInterestsLastChecked instanceof DateTime
                && $mailMotorInterestsLastChecked > new DateTime('-8 hours')
            ) {
                return $mailMotorInterests;
            }

            $mailMotorInterests = $this->subscriber->getInterests(
                $this->moduleSettings->get('Mailmotor', 'list_id_' . FrontendLocale::frontendLanguage())
            );

            // Has interests
            if (empty($mailMotorInterests) || !is_array($mailMotorInterests)) {
                return $interests;
            }

            foreach ($mailMotorInterests as $categoryId => $categoryInterest) {
                if (empty($categoryInterest['children']) || !is_array($categoryInterest['children'])) {
                    continue;
                }

                foreach ($categoryInterest['children'] as $categoryChildId => $categoryChildTitle) {
                    // Add interest value for checkbox
                    $interests[$categoryChildTitle] = $categoryChildId;
                }
            }
            $this->moduleSettings->set('Mailmotor', 'interests', $interests);
            $this->moduleSettings->set('Mailmotor', 'interests_last_checked', new DateTime());
        } catch (NotImplementedException $e) {
            // Fallback for when no mail-engine is chosen in the Backend
            $this->moduleSettings->set('Mailmotor', 'check_interests', false);

            return [];
        }

        return $interests;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
            'validation_groups' => function (FormInterface $form) {
                // Define overwrite interests
                $overwriteInterests = $this->moduleSettings->get('Mailmotor', 'overwrite_interests', true);
                if (!empty($this->interests) && $overwriteInterests) {
                    return ['Default', 'has_interests'];
                }

                return ['Default'];
            },
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'subscribe';
    }
}
