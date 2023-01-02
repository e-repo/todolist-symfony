import { Task } from "@/pages/task/types"
import { Ref, ref } from "vue"

const taskPublishedList = ref<Task[]>([])

export function useReactiveTaskList(): Ref<Task[]>
{
    return taskPublishedList
}
