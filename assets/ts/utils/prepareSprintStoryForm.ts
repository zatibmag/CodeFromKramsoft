import { IList, ISprint, ITask } from '../interfaces';

export const prepareSprintStoryForm = (lists: IList[], sprint: ISprint): FormData => {
    const formData = new FormData();

    lists.map((list: IList) => {
        if (list.id === sprint.listDoneId) {
            if (sprint.capacityType === 0) {
                const sumStoryPoints = list.tasks.reduce((totalStoryPoints: number, task: ITask) => {
                    return totalStoryPoints + task.storyPoints;
                }, 0);
                formData.append('sprint_story[capacity]', sumStoryPoints.toString());
            } else {
                formData.append('sprint_story[capacity]', list.tasks.length.toString());
            }
        }
    });

    return formData;
};
