<style>
.accordion {
  max-width: 600px;
  margin: 2rem auto;
  color:#fff;
}

.accordion-title {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  cursor: pointer;
  background-color: #CC131F;
}

.accordion-title:hover {
  background-color: #FF131F;
}

.accordion-title,
.accordion-content {
  padding: 1rem;
}

.accordion-content {
  background-color: #CC131F;
}

</style>

@CAppReact('Accordion' ,['data'=>$data])
