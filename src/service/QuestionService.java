package service;


import helper.HibernateUtil;

import java.lang.reflect.InvocationTargetException;
import java.util.List;

import javax.persistence.EntityManager;
import javax.ws.rs.Consumes;
import javax.ws.rs.DELETE;
import javax.ws.rs.GET;
import javax.ws.rs.POST;
import javax.ws.rs.PUT;
import javax.ws.rs.Path;
import javax.ws.rs.PathParam;
import javax.ws.rs.Produces;
import javax.ws.rs.core.MediaType;

import model.Questions;

import org.apache.commons.beanutils.BeanUtils;

@Path("/questions")
public class QuestionService {
	@GET
	@Path("{id}")
	@Produces({MediaType.APPLICATION_JSON})
	public List<Questions> getQuestions(@PathParam("id") Integer seriesId) {
		List<Questions> questions = HibernateUtil.getResults(
				"select q from Questions q where q.seriesId = ?", new Object[] {seriesId} );
		return questions;
	}


	@PUT
	@Path("/{id}")
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public String updateQuestion(Questions q, @PathParam("id") Long id) {
		EntityManager em = HibernateUtil.getEntityManagerFactory().createEntityManager();
		Questions existing = em.find(Questions.class, id);
		if (existing == null) {
			System.err.println("Unable to find Question with ID = " + id);
			return "{success: false, message: 'Unable to find Question', data: []}";
		}
		
		em.getTransaction().begin();

		try {
			BeanUtils.copyProperties(existing, q);
		} catch (IllegalAccessException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (InvocationTargetException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		em.persist(existing);
		em.getTransaction().commit();
		return "{success: true, message: 'Updated', data: []}";
	}
	
	@POST
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public String addQuestion(Questions q) {
		EntityManager em = HibernateUtil.getEntityManagerFactory()
				.createEntityManager();
		em.getTransaction().begin();
		em.persist(q);
		em.getTransaction().commit();
		return "{success: true, message: 'Added', data: []}";
	}
	
	@DELETE
	@Path("/{id}")
	@Produces(MediaType.APPLICATION_JSON)
	@Consumes(MediaType.APPLICATION_JSON)
	public String deleteQuestion(@PathParam("id") Long id) {
		EntityManager em = HibernateUtil.getEntityManagerFactory().createEntityManager();
		Questions existing = em.find(Questions.class, id);
		if (existing == null) {
			System.err.println("Unable to find Question with ID = " + id);
			return "{success: false, message: 'Unable to find Question', data: []}";
		}
		
		int result = HibernateUtil.execute("delete from Question q where q.id = ?", 
				new Object[] {id});
		System.out.println("RESULT = " + result);
		return "{success: true, message: 'Deleted', data: []}";
	}
}
