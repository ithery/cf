<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f9;
    }

    .container {
        position: relative;
        width: 100%;
        height: 90vh;
        overflow: auto;
        border: 1px solid #ddd;
        background-color: #fff;
    }

    .node {
        border: 1px solid #007bff;
        border-radius: 8px;
        padding: 10px;
        background-color: #e9f5ff;
        position: absolute;
        cursor: move;
        user-select: none;
        min-width: 120px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .line {
        position: absolute;
        border-top: 2px solid #007bff;
        z-index: 1;
    }

    .node:hover .delete-btn {
        display: inline;
    }

    .delete-btn {
        display: none;
        position: absolute;
        top: 0;
        right: 0;
        background: red;
        color: white;
        border: none;
        cursor: pointer;
        padding: 2px 5px;
        border-radius: 50%;
    }

    .edit-dialog {
        position: fixed;
        top: 20%;
        left: 50%;
        transform: translate(-50%, -20%);
        background: white;
        padding: 20px;
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .edit-dialog input {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .edit-dialog button {
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        background-color: #007bff;
        color: white;
        margin-right: 10px;
    }

    .edit-dialog button.cancel {
        background-color: #6c757d;
    }
</style>

<div x-data="flowEditor()">
    <div class="container">
        <!-- Nodes -->
        <template x-for="node in nodes" :key="node.id">
            <div class="node" :style="{ top: node.y + 'px', left: node.x + 'px' }" @mousedown="startDrag($event, node)"
                @dblclick="editNode(node)">
                <button class="delete-btn" @click.stop="removeNode(node)">X</button>
                <div x-text="node.label"></div>
            </div>
        </template>

        <!-- Lines -->
        <template x-for="line in lines" :key="line.id">
            <div class="line"
                :style="{ top: line.y1 + 'px', left: line.x1 + 'px', width: line.width + 'px', height: '2px' }">
            </div>
        </template>
    </div>

    <button @click="addNode()">Add Node</button>

    <!-- Edit Node Dialog -->
    <div x-show="editMode" class="edit-dialog">
        <h3>Edit Node</h3>
        <input type="text" x-model="currentNode.label" placeholder="Enter node label" />
        <button @click="saveNode()">Save</button>
        <button class="cancel" @click="cancelEdit()">Cancel</button>
    </div>
</div>
@CAppPushScript
<script>
    function flowEditor() {
        return {
            nodes: [{
                    id: 1,
                    label: 'Start',
                    x: 100,
                    y: 100
                },
                {
                    id: 2,
                    label: 'Step 1',
                    x: 300,
                    y: 100
                },
                {
                    id: 3,
                    label: 'Step 2',
                    x: 300,
                    y: 300
                },
            ],
            lines: [{
                    id: 1,
                    x1: 150,
                    y1: 120,
                    x2: 300,
                    y2: 120,
                    width: 150
                },
                {
                    id: 2,
                    x1: 300,
                    y1: 120,
                    x2: 300,
                    y2: 300,
                    width: 2
                },
            ],
            currentNode: null,
            editMode: false,

            startDrag(event, node) {
                const startX = event.clientX;
                const startY = event.clientY;
                const originalX = node.x;
                const originalY = node.y;

                const onMouseMove = (e) => {
                    const dx = e.clientX - startX;
                    const dy = e.clientY - startY;
                    node.x = originalX + dx;
                    node.y = originalY + dy;
                    this.updateLines();
                };

                const onMouseUp = () => {
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                };

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            },

            addNode() {
                const newNodeId = this.nodes.length ? Math.max(...this.nodes.map(n => n.id)) + 1 : 1;
                this.nodes.push({
                    id: newNodeId,
                    label: `Node ${newNodeId}`,
                    x: Math.random() * (this.$el.offsetWidth - 120),
                    y: Math.random() * (this.$el.offsetHeight - 120)
                });
                this.updateLines();
            },

            removeNode(nodeToRemove) {
                this.nodes = this.nodes.filter(node => node.id !== nodeToRemove.id);
                this.lines = this.lines.filter(line => line.x1 !== nodeToRemove.x && line.x2 !== nodeToRemove.x);
            },

            editNode(node) {
                this.currentNode = node;
                this.editMode = true;
            },

            saveNode() {
                this.editMode = false;
            },

            cancelEdit() {
                this.editMode = false;
                this.currentNode = null;
            },

            updateLines() {
                // Simple logic to update lines based on node positions
                this.lines = [{
                        id: 1,
                        x1: this.nodes[0].x + 60,
                        y1: this.nodes[0].y + 30,
                        x2: this.nodes[1].x + 60,
                        y2: this.nodes[1].y + 30,
                        width: Math.abs(this.nodes[1].x - this.nodes[0].x)
                    },
                    {
                        id: 2,
                        x1: this.nodes[1].x + 60,
                        y1: this.nodes[1].y + 30,
                        x2: this.nodes[2].x + 60,
                        y2: this.nodes[2].y + 30,
                        width: Math.abs(this.nodes[2].y - this.nodes[1].y)
                    },
                ];
            }
        };
    }
</script>
@CAppEndPushScript
