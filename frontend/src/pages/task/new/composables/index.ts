import { Task } from "@/pages/task/types"
import { Ref, ref } from "vue"

const taskPublishedList = ref<Task[] | null>([])

export function useReactiveTaskList(): Ref<Task[] | null>
{
    return taskPublishedList
}
