export default function (Alpine) {
    Alpine.magic('message', () => {
        return (msg) => cresenity.message('info', msg);
    });
}
