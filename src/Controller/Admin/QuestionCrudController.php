<?php

namespace App\Controller\Admin;

use App\Entity\Question;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

use function sprintf;

class QuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Question::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();
        yield Field::new('name');
        yield AssociationField::new('topic');
        yield TextareaField::new('question')
            ->hideOnIndex();
        yield Field::new('votes', 'Total votes')
            ->setTextAlign('right');
        yield AssociationField::new('askedBy')
            ->autocomplete()
            ->formatValue(static function ($value, Question $question) {
                if (!$user = $question->getAskedBy()) {
                    return null;
                }

                return sprintf('%s&nbsp;(%s)', $user->getEmail(), $user->getQuestions()->count());
            })
            ->setQueryBuilder(static function (QueryBuilder $queryBuilder): void {
                $queryBuilder->andWhere('entity.enabled = :enabled')
                    ->setParameter('enabled', true);
            });
        yield Field::new('createdAt')
            ->hideOnForm();
    }
}
