<style>
    .cursor-grab {
        cursor: -webkit-grab;
        cursor: grab;
    }

    .tasks {
        min-height: 450px;
    }
</style>
<div x-data="kanbanData()">
    <div class="row">
        <template x-for="(board, boardIndex) in boards" x-key="boardIndex">
            <div class="col-12 col-lg-4">
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h3 class="card-title h5 mb-1" x-text="board.name"></h3>
                        <small class="mb-0 text-muted" x-text="'Board ' + board.name"></small>
                    </div>
                    <div class="card-body">
                        <div class="tasks">
                            <template x-for="(task, taskIndex) in board.tasks" x-key="boardIndex">
                                <div class="card mb-3 cursor-grab">
                                    <div class="card-body">
                                        <p x-text="task.subject"></p>
                                        <p class="small text-muted" x-text="task.content"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>


    </div>
</div>
@CAppPushScript
<script>
    function kanbanData() {
        return {
            boards: @json($data),
            drake:null,
            init() {
                this.$nextTick(()=>{
                    const dragulaConfig = {};
                    const tasksElements = Array.prototype.slice.call(
                        document.querySelectorAll('.tasks')
                    );

                    if (this.drake === null) {
                        this.drake = dragula(tasksElements, dragulaConfig);
                        this.drake.on('drag', (el) => {
                            el.classList.add('is-moving');
                        });

                        this.drake.on('dragend', (el) => {
                            el.classList.remove('is-moving');
                            window.setTimeout(() => {
                                el.classList.add('is-moved');
                                window.setTimeout(() => {
                                    el.classList.remove('is-moved');
                                }, 600);
                            }, 100);
                        });

                        this.drake.on(
                            'drop',
                            async function(
                                el,
                                target,
                                source /*sibling: AnyVariable*/
                            ) {
                                const ticketElement = el;
                                const targetElement = target;
                                const sourceElement = source;

                                // const ticketId = ticketElement.getAttribute('ticket-id');
                                // const toTicketStateId =
                                //     targetElement.getAttribute('ticket-state-id');
                                // const fromTicketStateId =
                                //     sourceElement.getAttribute('ticket-state-id');
                                // const toDataOrderTicketId = [];
                                // const fromDataOrderTicketId = [];
                                // for (let i = 0; i < targetElement.children.length; i++) {
                                //     toDataOrderTicketId.push(
                                //         targetElement.children[i].getAttribute('ticket-id')
                                //     );
                                // }
                                // for (let i = 0; i < sourceElement.children.length; i++) {
                                //     fromDataOrderTicketId.push(
                                //         sourceElement.children[i].getAttribute('ticket-id')
                                //     );
                                // }

                                // const postData = {
                                //     ticketId: ticketId,
                                //     toTicketStateId: toTicketStateId,
                                //     fromTicketStateId: fromTicketStateId,
                                //     toDataOrderTicketId: toDataOrderTicketId,
                                //     fromDataOrderTicketId: fromDataOrderTicketId
                                // };
                                // await pb.api.request('UpdateTicketOrder', postData);
                            }
                        );
                    }
                });

            }
        }
    }
</script>

@CAppEndPushScript
